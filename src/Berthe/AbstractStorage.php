<?php

/**
 * Class definition for Berthe abstract Storage Berthe_Storage
 *
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0
 * @filesource Berthe/Storage.php
 * @package Berthe
 */
abstract class Berthe_AbstractStorage {
    const STORAGE_GUID = 'Berthe_AbstractStorage_Package_GUID';

    const STORE_LEVEL_1 = 'array_php';
    const STORE_VOLATILE_WITH_TTL = 'ramcache';
    const STORE_PERSISTENT = 'database';

    /**
     * @var Berthe_Context
     */
    public $context = null;

    /**
     * Set to true to force cache regeneration
     * @var boolean
     */
    public $ignoreCache = false;

    /**
     * Set to true if the frontcache has to know and register the object for further flush with its reloader
     * @see Berthe_FrontCache_Reloader::addRelation
     * @see Berthe_FrontCache_Reloader::flushRelation
     * @var boolean
     */
    protected $isFrontCacheAware = true;

    /**
     * @var Berthe_AbstractStore[]
     */
    protected $stores = array();
    /**
     * @var Berthe_AbstractReader
     */
    protected $_reader = null;
    /**
     * @var Berthe_AbstractWriter
     */
    protected $_writer = null;
    /**
     * @var Berthe_Memcached
     */
    protected $_memcached = null;
    /**
     * Set to true to ignore object existence in $_objects when fetching
     * @var boolean
     */
    protected $ignoreCacheLevel1 = false;
    /**
     * Cache key for each storage implementation
     * @var string
     */
    protected $_cacheKey = null;

    /**
     * @return Berthe_StoreDatabase
     */
    protected function getStorePersistent() {
        return $this->stores[self::STORE_PERSISTENT];
    }
    /**
     * @return Berthe_AbstractReader
     */
    protected function getReader() {
        return $this->_reader;
    }

    /**
     * @param bool|null $isAware
     * @return bool
     */
    protected function isFrontCacheAware($isAware = null) {
        if ($isAware !== null) {
            $this->isFrontCacheAware = (bool) $isAware;
        }
        return $this->isFrontCacheAware;
    }

    /**
     * @return Berthe_AbstractWriter
     */
    protected function getWriter() {
        return $this->_writer;
    }

    /**
     * @return Berthe_StoreMemcached
     */
    protected function getStoreVolatile() {
        return $this->stores[self::STORE_VOLATILE_WITH_TTL];
    }

    /**
     * @return Berthe_StoreArray
     */
    protected function getStoreLevel1() {
        return $this->stores[self::STORE_LEVEL_1];
    }

    /**
     * Constructor
     */
    public function __construct(Berthe_Context $context = null) {
        $this->context = $context;
        $this->_initConnectors();
        $this->_initCustoms();
    }

    /**
     *
     */
    protected function _initStores() {
        $site = $this->context ? $this->context->getSite() : null;
        $cacheKey = $this->_cacheKey;
        $packageGUID = $this->getStorageGUID();

        $this->stores[self::STORE_LEVEL_1] = new Berthe_StoreArray();
        $this->stores[self::STORE_VOLATILE_WITH_TTL] = new Berthe_StoreMemcached($site, $cacheKey, $packageGUID);

        if ($this->ignoreCache) {
            $this->stores[self::STORE_LEVEL_1]->isEnabled(false);
            $this->stores[self::STORE_VOLATILE_WITH_TTL]->isEnabled(false);
        }

        $this->_memcached = $this->stores[self::STORE_VOLATILE_WITH_TTL]->getCacheEngine();
    }

    /**
     * Starts the connectors
     */
    protected function _initConnectors() {
        $this->_initStores();
        $this->_initDatabaseConnections();
        if ($this->_reader && $this->_writer) {
            $this->stores[self::STORE_PERSISTENT] = new Berthe_StoreDatabase($this->_reader, $this->_writer);
        }
    }

    /**
     * Initialize custom specific stuff
     */
    protected function _initCustoms() {

    }

    /**
     * Inits the database connections
     */
    abstract protected function _initDatabaseConnections();

    /**
     * returns the package name guid
     */
    final protected function getStorageGUID() {
        if (static::STORAGE_GUID != self::STORAGE_GUID) {
            return static::STORAGE_GUID;
        }
        throw new RuntimeException('The package used has no GUID');
    }

    /**
     * @param int $id
     * @return Berthe_AbstractVO
     */
    public function getOriginalObject($id) {
        $this->ignoreAllCache(true);
        $originalObject = $this->getById($id);
        $this->ignoreAllCache(false);

        return $originalObject;
    }

    /**
     * @param int $id
     * @return Berthe_AbstractVO
     */
    public function getById($id) {
        $id = (int)$id;
        if (!is_numeric($id)) {
            throw new Exception(get_called_class() . '::' . __FUNCTION__ . " : Id should be an integer, given '" . $id . "'");
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
     * @return Berthe_AbstractVO[]
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
     * @return Berthe_AbstractVO[]
     */
    public function getColumnByIds(array $ids = array(), $columnName = 'id') {
        $ids = array_filter(array_unique($ids));

        $_res = $this->_reader->selectColByIds($ids, $columnName);
        // In order to keep the same order than given in method parameter

        if(count($ids) != count($_res)) {
            trigger_error(get_called_class() . '::' . __FUNCTION__ . '() : Did not get all values', E_USER_NOTICE);
        }

        return $_res;
    }

    /**
     * @param array $ids
     * @return Berthe_AbstractVO[]
     */
    public function getColumnByIdsPreserveIds(array $ids = array(), $columnName = 'id') {
        $ids = array_filter(array_unique($ids));

        $_res = $this->_reader->selectColByIdsPreserveIds($ids, $columnName);
        // In order to keep the same order than given in method parameter

        if(count($ids) != count($_res)) {
            trigger_error(get_called_class() . '::' . __FUNCTION__ . '() : Did not get all values', E_USER_NOTICE);
        }

        return $_res;
    }

    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function getByPaginator(Fetcher $paginator) {
        $count = $this->_reader->selectCountByPaginator($paginator);
        $ids = $this->_reader->selectByPaginator($paginator);
        $results = $this->getByIds($ids);
        $paginator->setTtlCount($count);
        $paginator->set($results);
        return $paginator;
    }

    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher
     */
    public function getColumnByPaginator(Fetcher $paginator, $columnName = 'id') {
        $count = $this->_reader->selectCountByPaginator($paginator);
        $ids = $this->_reader->selectByPaginator($paginator);
        $results = $this->getColumnByIds($ids, $columnName);
        $paginator->setTtlCount($count);
        $paginator->set($results, false);
        return $paginator;
    }

    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher
     */
    public function getColumnByPaginatorPreserveIds(Fetcher $paginator, $columnName = 'id') {
        $count = $this->_reader->selectCountByPaginator($paginator);
        $ids = $this->_reader->selectByPaginator($paginator);
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
     * @param Fetcher $paginator
     * @return string sql
     */
    public function getSqlByPaginator(Fetcher $paginator) {
        return $this->_reader->getSqlByPaginator($paginator);
    }

    /**
     * TODO optimize that one !
     * @param Berthe_AbstractVO $vo
     * @param Fetcher $paginator
     * @param int $nbBefore
     * @param int $nbAfter
     * @param bool $loop
     * @return array array[voBefore[], voAfter[]]  BEFORE / AFTER
     */
    public function getNextAndPreviousByPaginator(Berthe_AbstractVO $vo, Fetcher $paginator, $nbBefore = 1, $nbAfter = 1, $loop = false) {
        $page = $paginator->getPage();
        $nbByPage = $paginator->getNbByPage();

        $paginator->setPage(-1);
        $paginator->setNbByPage(-1);
        $ids = $this->_reader->selectByPaginator($paginator);

        $paginator->setPage($page);
        $paginator->setNbByPage($nbByPage);

        $_keys = array_keys($ids, $vo->id);

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
            if (in_array($vo->id, $beforeIds)) {
                $before[$vo->id] = $vo;
            }
            else {
                $after[$vo->id] = $vo;
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
        $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        foreach($this->stores as $storeType => $store) {
            if(count($_idsNotFound) > 0) {
                $objects = $store->load($_idsNotFound);

                if (count($objects) > 0) {
                    switch($storeType) {
                        case self::STORE_VOLATILE_WITH_TTL :
                            $this->getStoreLevel1()->saveMulti($objects);
                            break;
                        case self::STORE_PERSISTENT :
                            $this->getStoreLevel1()->saveMulti($objects);
                            $this->getStoreVolatile()->saveMulti($objects);
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
            $this->getStoreVolatile()->isEnabled(!((bool)$shallIgnore));
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
            $this->getStoreLevel1()->isEnabled(!((bool)$shallIgnore));
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
     * @param Berthe_AbstractVO $vo
     * @return boolean
     */
    public function save(Berthe_AbstractVO $vo) {
        $storePersistent = $this->getStorePersistent();
        $storeVolatile = $this->getStoreVolatile();
        $storeLevel1 = $this->getStoreLevel1();

        $ret = $storePersistent->save($vo);
        if ($ret) {
            $storeVolatile->save($vo);
            $storeLevel1->save($vo);
        }
        else {
            throw new RuntimeException("Couldn't save object into database");
        }

        return $ret;
    }

    /**
     * Deletes a VO
     * @param Berthe_AbstractVO $vo
     */
    public function delete(Berthe_AbstractVO $vo) {
        // We reverse because the last storage is supposed to be the persistent one, and others are faster/caching
        $storesReversed = array_reverse($this->stores);

        $ret = true;
        foreach($storesReversed as /* @var $store Berthe_AbstractStore */ $store) {
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

    /**
     * @param string $methodName
     * @param array $arguments
     * @param string $callback
     * @param int $ttl
     * @return mixed
     */
    protected function cacheRetrieverSet($methodName, $arguments, $callback, $ttl = 0) {
        $key = $this->getStorageGUID() . $methodName . ':' . md5(serialize($arguments));

        $ignoreCache = $this->ignoreCache();
        $foundData = false;

        if (!$ignoreCache){
            $data = $this->_memcached->get($key);
            if ($this->_memcached->getResultCode() !== Memcached::RES_NOTFOUND) {
                $result = unserialize($data);
                $foundData = true;
            }
        }

        if (!$foundData) {
            $result = call_user_func_array(array($this->_reader, $callback), $arguments);
            $this->_memcached->set($key, serialize($result), $ttl);
        }

        return $result;
    }
}