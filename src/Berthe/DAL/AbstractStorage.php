<?php

namespace Berthe\DAL;

use Berthe\Exception\NotUniqueResultException;
use Berthe\VO;
use Berthe\Fetcher;

abstract class AbstractStorage implements Storage
{
    /**
     * Storage id
     * @var string|null
     */
    protected $storageGUID = null;

    /**
     * @var Store[]
     */
    protected $stores = array();

    /** @var string|null key of the primary store */
    protected $primaryStore;

    /**
     * Set storage GUID
     * @param string $guid
     * @return Storage
     */
    public function setStorageGUID($guid)
    {
        $this->storageGUID = $guid;
        return $this;
    }

    /**
     * @param Store $store
     * @param bool  $isPrimary
     * @return $this
     */
    public function addStore(Store $store, $isPrimary = true)
    {
        $storeName = uniqid();
        $this->stores[$storeName] = $store;
        if ($isPrimary) {
            $this->primaryStore = $storeName;
        }

        return $this;
    }

    /**
     * @param int $id
     * @return VO
     */
    public function getObjectFromPrimaryStore($id)
    {
        $results = $this->getPrimaryStore()->load(array($id));
        if (!empty($results)) {
            return reset($results);
        }
        return null;
    }

    /**
     * @param int $id
     * @return VO
     */
    public function getById($id)
    {
        $id = (int)$id;
        $object = $this->load(array($id));

        if (array_key_exists($id, $object)) {
            return $object[$id];
        }

        throw new \Berthe\Exception\NotFoundException(sprintf("%s::%s couldn't find object with id='%d'", get_called_class(), __FUNCTION__, $id));
    }

    /**
     * @param array $ids
     * @return VO[]
     */
    public function getByIds(array $ids = array())
    {
        $ids = array_filter(array_unique($ids));

        $_res = $this->load($ids);
        // In order to keep the same order than given in method parameter
        $_out = array();
        foreach ($ids as &$v) {
            if (array_key_exists($v, $_res)) {
                $_out[$v] = $_res[$v];
            }
        }

        if (count($ids) != count($_out)) {
            trigger_error(get_called_class() . '::' . __FUNCTION__ . '() : Did not get all VOs : ' . implode(',', $ids), E_USER_NOTICE);
        }
        return $_out;
    }

    /**
     * @inheritdoc
     */
    public function getIdsByFetcher(Fetcher $fetcher)
    {
        return $this->getPrimaryStore()->getIdsByFetcher($fetcher);
    }

    /**
     * @inheritdoc
     */
    public function getCountByFetcher(Fetcher $fetcher)
    {
        return $this->getPrimaryStore()->getCountByFetcher($fetcher);
    }

    /**
     * @todo  handle all cases : how to decide which store to load from ? merge ids ? what about object invalidation ? ...
     * @inheritdoc
     */
    public function getByFetcher(Fetcher $fetcher)
    {
        $primaryStore = $this->getPrimaryStore();

        if ($fetcher->hasLimit()) {
            $count = $primaryStore->getCountByFetcher($fetcher);
            $fetcher->setTtlCount($count);
            if ($count === 0) {
                $fetcher->clear();
                return $fetcher;
            }
        }

        $ids = $primaryStore->getIdsByFetcher($fetcher);
        if (empty($ids)) {
            $fetcher->clear();
            return $fetcher;
        }

        $objects = $this->getByIds($ids);
        $fetcher->set($objects);

        // set ttlcount for unlimited results fetcher
        if (!$fetcher->hasLimit()) {
            $fetcher->setTtlCount($fetcher->count());
        }

        return $fetcher;
    }

    /**
     * @inheritdoc
     */
    public function getFirstByFetcher(Fetcher $fetcher)
    {
        $fetcher->setPage(1);
        $fetcher->setNbByPage(1);
        $ids = $this->getIdsByFetcher($fetcher);
        if (empty($ids)) {
            return null;
        }
        return $this->getById(reset($ids));
    }

    /**
     * @inheritdoc
     */
    public function getUniqueByFetcher(Fetcher $fetcher)
    {
        $fetcher->setPage(1);
        $fetcher->setNbByPage(2);
        $ids = $this->getIdsByFetcher($fetcher);
        if (empty($ids)) {
            return null;
        }
        if (count($ids) === 2) {
            throw new NotUniqueResultException();
        }
        return $this->getById(reset($ids));
    }

    /**
     * Saves a VO
     * @param VO $vo
     * @return boolean
     */
    public function save(VO $vo)
    {
        $store = $this->getPrimaryStore();
        $success = $store->save($vo);

        if (!$success) {
            throw new \RuntimeException("Couldn't store object in primary store", 500);
        }

        foreach ($this->stores as $storeName => $store) {
            if (!$this->isPrimaryStore($storeName)) {
                $store->save($vo);
            }
        }

        return $success;
    }


    /**
     * Deletes a VO
     * @param VO $vo
     */
    public function delete(VO $vo)
    {
        foreach ($this->stores as $storeName => $store) {
            if (!$this->isPrimaryStore($storeName)) {
                $ret = $store->delete($vo);
                if (!$ret) {
                    throw new \RuntimeException(sprintf("Couldn't remove object from non-primary store, aborting"), 500);
                }
            }
        }

        $primaryStore = $this->getPrimaryStore();
        $success = $primaryStore->delete($vo);

        if (!$success) {
            throw new \RuntimeException(sprintf("Couldn't remove object from primary store, aborting"), 500);
        }

        return true;
    }

    /**
     * Deletes a VO by its id
     * @param integer $id
     */
    public function deleteById($id)
    {
        try {
            $obj = $this->getById($id);
            $this->delete($obj);
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * returns the package name guid
     */
    final protected function getStorageGUID()
    {
        if ($this->storageGUID) {
            return $this->storageGUID;
        }

        throw new \RuntimeException('The package used has no GUID');
    }

    /**
     * @return Store|null
     */
    protected function getPrimaryStore()
    {
        if ($this->primaryStore && isset($this->stores[$this->primaryStore])) {
            return $this->stores[$this->primaryStore];
        }
        throw new \RuntimeException("Primary store is missing", 1);
    }

    /**
     * @param string $storeName
     * @return bool
     */
    protected function isPrimaryStore($storeName)
    {
        return $this->primaryStore === $storeName;
    }

    /**
     * Try to load requested objects from stores
     * @param int[] $ids
     */
    protected function load(array $ids = array())
    {
        $idsNotFound = array_filter($ids);

        $output = array();

        $stores = $this->stores;

        foreach ($this->stores as $storeName => $store) {
            if (!empty($idsNotFound)) {
                $objects = $store->load($idsNotFound);

                $loadedKeys = array_keys($objects);
                $remainingKeys = array_values(array_diff($idsNotFound, $loadedKeys));
                $idsNotFound = $remainingKeys;
                $output = $output + $objects;

                if (!empty($objects) && $store == $this->primaryStore) {
                    foreach ($stores as $storeName2 => $store2) {
                        if ($storeName2 !== $storeName) {
                            $ret = $store2->saveMulti($objects);
                        }
                    }
                }
            }
        }


        return $output;
    }
}
