<?php

namespace Berthe\DAL;

use Berthe\VO;
use Berthe\Fetcher;

abstract class AbstractStorage implements Storage {
    /**
     * Storage id
     * @var string
     */
    protected $storageGUID = null;

    /**
     * @var Store[]
     */
    protected $stores = array();

    protected $primaryStore = null;

    /**
     * Set storage GUID
     * @param string $guid
     * @return Storage
     */
    public function setStorageGUID($guid) {
        $this->storageGUID = $guid;
        return $this;
    }

    public function addStore(Store $store, $isPrimary = true) {
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
    public function getObjectFromPrimaryStore($id) {
        if ($this->getPrimaryStore()) {
            $originalObject = $this->getPrimaryStore()->getById($id);
            return $originalObject;
        }

        throw new \RuntimeException("No primary store provided in storage", 500);
    }

    /**
     * @param int $id
     * @return VO
     */
    public function getById($id) {
        $id = (int)$id;
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException(sprintf("%s::%s only accepts integer, '%s' given", get_called_class(), __FUNCTION__, $id));
        }

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
    public function getByIds(array $ids = array()) {
        $ids = array_filter(array_unique($ids));

        $_res = $this->load($ids);
        // In order to keep the same order than given in method parameter
        $_out = array();
        foreach($ids as &$v) {
            if (array_key_exists($v, $_res)) {
                $_out[$v] = $_res[$v];
            }
        }

        if(count($ids) != count($_out)) {
            trigger_error(get_called_class() . '::' . __FUNCTION__ . '() : Did not get all VOs : ' . implode(',', $ids), E_USER_NOTICE);
        }
        return $_out;
    }

    /**
     * @todo  handle all cases : how to decide which store to load from ? merge ids ? what about object invalidation ? ...
     * @param Fetcher $fetcher
     * @return Fetcher
     */
    public function getByPaginator(Fetcher $fetcher) {
        if ($this->getPrimaryStore()) {
            $count = $this->getPrimaryStore()->getCountByFetcher($fetcher);
            $ids = $this->getPrimaryStore()->getIdsByFetcher($fetcher);
            $objects = $this->getByIds($ids);

            $fetcher->setTtlCount($count);
            $fetcher->set($objects);
            return $fetcher;
        }
    }

    /**
     * Saves a VO
     * @param VO $vo
     * @return boolean
     */
    public function save(VO $vo) {
        $store = $this->getPrimaryStore();
        $success = $store->save($vo);

        if (!$success) {
            throw new \RuntimeException("Couldn't store object in primary store", 500);
        }

        foreach($this->stores as $storeName => $store) {
            if (!$this->isPrimaryStore($storeName)) {
                $store->save($vo);
            }
        }

        return $ret;
    }


    /**
     * Deletes a VO
     * @param VO $vo
     */
    public function delete(VO $vo) {
        foreach($this->stores as $storeName => $store) {
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
     * @param integer $vo
     */
    public function deleteById($id) {
        try {
            $obj = $this->getById($id);
            $this->delete($obj);
            return true;
        }
        catch(\Exception $e) {
            throw $e;
        }
    }

    /**
     * returns the package name guid
     */
    final protected function getStorageGUID() {
        if ($this->storageGUID) {
            return $this->storageGUID;
        }

        throw new \RuntimeException('The package used has no GUID');
    }


    protected function getPrimaryStore() {
        if ($this->primaryStore && array_key_exists($this->primaryStore, $this->stores)) {
            return $this->stores[$this->primaryStore];
        }
        return null;
    }

    protected function isPrimaryStore($storeName) {
        return $this->primaryStore === $storeName;
    }

    /**
     * Try to load requested objects from stores
     * @param array $ids
     */
    protected function load(array $ids = array()) {
        $idsNotFound = array_filter($ids);

        $output = array();

        $stores = $this->stores;

        foreach($this->stores as $storeName => $store) {
            if(count($idsNotFound) > 0) {
                $objects = $store->load($idsNotFound);

                $loadedKeys = array_keys($objects);
                $remainingKeys = array_values(array_diff($idsNotFound, $loadedKeys));
                $idsNotFound = $remainingKeys;
                $output = $output + $objects;

                if (count($objects)) {
                    foreach($stores as $storeName2 => $store2) {
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
