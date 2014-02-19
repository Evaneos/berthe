<?php

namespace Berthe\DAL;

abstract class AbstractStorage {
    const STORAGE_GUID = '\Berthe\AbstractStorage\Package_GUID';

    const STORE_LEVEL_1 = 'array_php';
    const STORE_VOLATILE_WITH_TTL = 'ramcache';
    const STORE_PERSISTENT = 'database';

    /**
     * Set to true to force cache regeneration
     * @var boolean
     */
    public $ignoreCache = false;

    /**
     * Set to true to ignore object existence in $_objects when fetching
     * @var boolean
     */
    public $ignoreCacheLevel1 = false;

    /**
     * @var AbstractStore[]
     */
    protected $stores = array();

    /**
     * @return StoreDatabase
     */
    public function getStorePersistent() {
        return array_key_exists(self::STORE_PERSISTENT, $this->stores) ? $this->stores[self::STORE_PERSISTENT] : null;
    }

    public function setStorePersistent(StoreDatabase $store) {
        $this->stores[self::STORE_PERSISTENT] = $store;
        return $this;
    }

    /**
     * @return AbstractStore
     */
    public function getStoreVolatile() {
        return array_key_exists(self::STORE_VOLATILE_WITH_TTL, $this->stores) ? $this->stores[self::STORE_VOLATILE_WITH_TTL] : null;
    }

    public function setStoreVolatile(AbstractStore $store) {
        $this->stores[self::STORE_VOLATILE_WITH_TTL] = $store;
        return $this;
    }

    /**
     * @return AbstractStore
     */
    public function getStoreLevel1() {
        return array_key_exists(self::STORE_LEVEL_1, $this->stores) ? $this->stores[self::STORE_LEVEL_1] : null;
    }

    public function setStoreLevel1(AbstractStore $store) {
        $this->stores[self::STORE_LEVEL_1] = $store;
        return $this;
    }

    /**
     * returns the package name guid
     */
    final protected function getStorageGUID() {
        if (static::STORAGE_GUID != self::STORAGE_GUID) {
            return static::STORAGE_GUID;
        }
        throw new \RuntimeException('The package used has no GUID');
    }

    /**
     * @param int $id
     * @return \Berthe\AbstractVO
     */
    public function getOriginalObject($id) {
        $this->ignoreAllCache(true);
        $originalObject = $this->getById($id);
        $this->ignoreAllCache(false);

        return $originalObject;
    }

    /**
     * @param int $id
     * @return \Berthe\AbstractVO
     */
    public function getById($id) {
        $id = (int)$id;
        if (!is_numeric($id)) {
            throw new \Exception(get_called_class() . '::' . __FUNCTION__ . " : Id should be an integer, given '" . $id . "'");
        }

        $object = $this->load(array($id));

        if (array_key_exists($id, $object)) {
            return $object[$id];
        }
        else {
            trigger_error(get_called_class() . '::' . __FUNCTION__ . '() : Could not find object with id ' . $id, E_USER_NOTICE);
            return null;
        }
    }

    /**
     * @param array $ids
     * @return \Berthe\AbstractVO[]
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
     * @param array $ids
     * @return \Berthe\AbstractVO[]
     */
    public function getColumnByIds(array $ids = array(), $columnName = 'id') {
        $ids = array_filter(array_unique($ids));

        $_res = $this->getStorePersistent()->getReader()->selectColByIds($ids, $columnName);
        // In order to keep the same order than given in method parameter

        if(count($ids) != count($_res)) {
            trigger_error(get_called_class() . '::' . __FUNCTION__ . '() : Did not get all values', E_USER_NOTICE);
        }

        return $_res;
    }

    /**
     * @param array $ids
     * @return \Berthe\AbstractVO[]
     */
    public function getColumnByIdsPreserveIds(array $ids = array(), $columnName = 'id') {
        $ids = array_filter(array_unique($ids));

        $_res = $this->getStorePersistent()->getReader()->selectColByIdsPreserveIds($ids, $columnName);
        // In order to keep the same order than given in method parameter

        if(count($ids) != count($_res)) {
            trigger_error(get_called_class() . '::' . __FUNCTION__ . '() : Did not get all values', E_USER_NOTICE);
        }

        return $_res;
    }

    /**
     * @param \Berthe\Fetcher $paginator
     * @return \Berthe\Fetcher
     */
    public function getByPaginator(\Berthe\Fetcher $paginator) {
        $count = $this->getStorePersistent()->getReader()->selectCountByPaginator($paginator);
        $ids = $this->getStorePersistent()->getReader()->selectByPaginator($paginator);
        $results = $this->getByIds($ids);
        $paginator->setTtlCount($count);
        $paginator->set($results);
        return $paginator;
    }

    /**
     * @param \Berthe\Fetcher $paginator
     * @param string $columnName
     * @return \Berthe\Fetcher
     */
    public function getColumnByPaginator(\Berthe\Fetcher $paginator, $columnName = 'id') {
        $count = $this->getStorePersistent()->getReader()->selectCountByPaginator($paginator);
        $ids = $this->getStorePersistent()->getReader()->selectByPaginator($paginator);
        $results = $this->getColumnByIds($ids, $columnName);
        $paginator->setTtlCount($count);
        $paginator->set($results, false);
        return $paginator;
    }

    /**
     * @param \Berthe\Fetcher $paginator
     * @param string $columnName
     * @return \Berthe\Fetcher
     */
    public function getColumnByPaginatorPreserveIds(\Berthe\Fetcher $paginator, $columnName = 'id') {
        $count = $this->getStorePersistent()->getReader()->selectCountByPaginator($paginator);
        $ids = $this->getStorePersistent()->getReader()->selectByPaginator($paginator);
        $results = $this->getColumnByIdsPreserveIds($ids, $columnName);

        $resultsSorted = array();
        foreach($ids as $id) {
            if (array_key_exists($id, $results)) {
                $resultsSorted[$id] = $results[$id];
            }
            else {
                $resultsSorted[$id] = null;
            }
        }

        $paginator->setTtlCount($count);
        $paginator->set($resultsSorted, true);
        return $paginator;
    }

    /**
     * @param \Berthe\Fetcher $paginator
     * @return string sql
     */
    public function getSqlByPaginator(\Berthe\Fetcher $paginator) {
        return $this->getStorePersistent()->getReader()->getSqlByPaginator($paginator);
    }

    /**
     * TODO optimize that one !
     * @param \Berthe\AbstractVO $vo
     * @param \Berthe\Fetcher $paginator
     * @param int $nbBefore
     * @param int $nbAfter
     * @param bool $loop
     * @return array array[voBefore[], voAfter[]]  BEFORE / AFTER
     */
    public function getNextAndPreviousByPaginator(\Berthe\AbstractVO $vo, \Berthe\Fetcher $paginator, $nbBefore = 1, $nbAfter = 1, $loop = false) {
        $page = $paginator->getPage();
        $nbByPage = $paginator->getNbByPage();

        $paginator->setPage(-1);
        $paginator->setNbByPage(-1);
        $ids = $this->getStorePersistent()->getReader()->selectByPaginator($paginator);

        $paginator->setPage($page);
        $paginator->setNbByPage($nbByPage);

        $_keys = array_keys($ids, $vo->getId());

        $position = reset($_keys);

        $beforeIds = $this->_fetchPrevious($position, $ids, $nbBefore, $loop);
        $afterIds = $this->_fetchNext($position, $ids, $nbAfter, $loop);
        return $this->_fetchNextAndPrevious($beforeIds, $afterIds);
    }

    /**
     * @param int $position
     * @param array $ids
     * @param int $nbBefore
     * @param bool $loop
     * @return array
     */
    protected function _fetchPrevious($position, array $ids, $nbBefore, $loop = false) {
        $beforeIds = array();
        $decal = 1;
        $maxKey = count($ids) - 1;
        while($nbBefore > 0) {
            if (array_key_exists($position - $decal, $ids)) {
                $beforeIds[] = $ids[$position - $decal];
            }
            elseif ($loop && array_key_exists($maxKey + ($position - $decal + 1), $ids)) {
                $beforeIds[] = $ids[$maxKey + ($position - $decal + 1)];
            }
            $decal++;
            $nbBefore--;
        }

        return $beforeIds;
    }

    /**
     * @param int $position
     * @param array $ids
     * @param int $nbAfter
     * @param bool $loop
     * @return array
     */
    protected function _fetchNext($position, array $ids, $nbAfter, $loop = false) {
        $afterIds = array();
        $decal = 1;
        $decalFromEdge = 0;
        while($nbAfter > 0) {
            if (array_key_exists($position + $decal, $ids)) {
                $afterIds[] = $ids[$position + $decal];
            }
            elseif ($loop && array_key_exists($decalFromEdge, $ids)) {
                $afterIds[] = $ids[$decalFromEdge++];
            }

            $decal++;
            $nbAfter--;
        }

        return $afterIds;
    }

    /**
     * @param array $beforeIds
     * @param array $afterIds
     * @return array
     */
    protected function _fetchNextAndPrevious(array $beforeIds, array $afterIds) {
        $before = array();
        $after = array();

        $vos = $this->getByIds(array_merge($beforeIds, $afterIds));

        foreach($vos as $vo) {
            if (in_array($vo->getId(), $beforeIds)) {
                $before[$vo->getId()] = $vo;
            }
            else {
                $after[$vo->getId()] = $vo;
            }
        }

        return array($before, $after);
    }

    /**
     * Try to load requested objects from stores
     * @param array $ids
     */
    protected function load(array $ids = array()) {
        $_idsNotFound = array_filter($ids);

        if ((defined('FORCE_CACHE') && FORCE_CACHE === true) || $this->ignoreCache) {
            $this->ignoreAllCache(true);
        }

        $output = array();

        foreach($this->stores as $storeType => $store) {
            if(count($_idsNotFound) > 0) {
                $objects = $store->load($_idsNotFound);

                if (count($objects) > 0) {
                    switch($storeType) {
                        case self::STORE_VOLATILE_WITH_TTL :
                            if ($this->getStoreLevel1()) {
                                $this->getStoreLevel1()->saveMulti($objects);
                            }
                            break;
                        case self::STORE_PERSISTENT :
                            if ($this->getStoreLevel1()) {
                                $this->getStoreLevel1()->saveMulti($objects);
                            }
                            if ($this->getStoreVolatile()) {
                                $this->getStoreVolatile()->saveMulti($objects);
                            }
                            break;
                    }
                }

                $loadedKeys = array_keys($objects);
                $remainingKeys = array_values(array_diff($_idsNotFound, $loadedKeys));
                $_idsNotFound = $remainingKeys;
                $output = $output + $objects;
            }
        }

        return $output;
    }

    /**
     * Determines if cache is to be forced
     * @return boolean
     */
    public function ignoreCache($shallIgnore = null) {
        if ($shallIgnore !== null) {
            $this->ignoreCache = (bool) $shallIgnore;
            if ($this->getStoreVolatile()) {
                $this->getStoreVolatile()->isEnabled(!((bool)$shallIgnore));
            }
        }

        return ($this->ignoreCache or (defined('FORCE_CACHE') and FORCE_CACHE));
    }

    /**
     * Set to true to ignore object existence in $_objects when fetching
     * @param boolean $shallIgnore
     */
    public function ignoreCacheLevel1($shallIgnore = null) {
        if ($shallIgnore !== null) {
            $this->ignoreCacheLevel1 = (bool) $shallIgnore;
            if ($this->getStoreLevel1()) {
                $this->getStoreLevel1()->isEnabled(!((bool)$shallIgnore));
            }
        }

        return $this->ignoreCacheLevel1;
    }

    /**
     * @param mixed $shallIgnore
     * @return type
     */
    public function ignoreAllCache($shallIgnore = null) {
        $ret1 = $this->ignoreCacheLevel1($shallIgnore);
        $ret2 = $this->ignoreCache($shallIgnore);

        return ($ret1 && $ret2);
    }

    /**
     * Saves a VO
     * @param \Berthe\AbstractVO $vo
     * @return boolean
     */
    public function save(\Berthe\AbstractVO $vo) {
        $storePersistent = $this->getStorePersistent();
        $storeVolatile = $this->getStoreVolatile();
        $storeLevel1 = $this->getStoreLevel1();

        $ret = $storePersistent->save($vo);
        if ($ret) {
            if ($storeVolatile) {
                $storeVolatile->save($vo);
            }
            if ($storeLevel1) {
                $storeLevel1->save($vo);
            }
        }
        else {
            throw new \RuntimeException("Couldn't save object into database");
        }

        return $ret;
    }

    /**
     * Deletes a VO
     * @param \Berthe\AbstractVO $vo
     */
    public function delete(\Berthe\AbstractVO $vo) {
        // We reverse because the last storage is supposed to be the persistent one, and others are faster/caching
        $storesReversed = array_reverse($this->stores);

        $ret = true;
        foreach($storesReversed as /* @var $store \Berthe\DAL\AbstractStore */ $store) {
            if ($ret) {
                $ret = $store->delete($vo);
            }
        }

        return $ret;
    }

    /**
     * Deletes a VO by its id
     * @param integer $vo
     */
    public function deleteById($id) {
        $ret = null;
        $obj = $this->load(array($id));
        if (is_array($obj) && count($obj) === 1) {
            $ret = $this->delete(reset($obj));
        }
        else {
            trigger_error('Couldn\'t load object which has for id ' . $id . ' in ' . get_called_class(), E_USER_NOTICE);
        }
        return $ret;
    }
}
