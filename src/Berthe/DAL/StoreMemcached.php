<?php

namespace Berthe\DAL;

class StoreMemcached extends AbstractStore {
    /**
     * The memcached accessor shared across all stores
     * @var \Memcached
     */
    protected $memcached = null;
    /**
     * @var boolean
     */
    protected $isPersistent = false;
    /**
     * @var boolean
     */
    protected $isTTLAble = true;
    /**
     * @var string
     */
    protected $cacheKey = null;
    /**
     * @var string
     */
    protected $memcachedName = null;

    /**
     * Returns the base key for memcached
     */
    protected function _getBaseMemcachedKey() {
        if (is_null($this->cacheKey)) {
            $this->cacheKey = 'berthe:' . $this->memcachedName . ':';
        }
        return $this->cacheKey;
    }

    /**
     * Appends the suffix to the base memcahced key
     * @return string
     */
    public function getMemcachedKey($suffix) {
        return $this->_getBaseMemcachedKey() . $suffix;
    }

    /**
     * @param array $ids
     * @return array id=>object
     */
    protected function _load(array $ids = array()) {
        if (!$this->memcached) {
            return array();
        }

        $_idsCopy = array_flip($ids);

        // Array transformation :  array[id] = id  INTO  array[memcachedId] = id
        array_walk($_idsCopy, function(&$value, $key, $context) {
            $value = $context->getMemcachedKey($key); // "ev:mc:obj:" . $value;
        }, $this);
        $_memcachedFormattedIds = array_flip($_idsCopy);

        // unserialized & store locally
        $_objectsFromCache = $this->memcached->getMulti(array_keys($_memcachedFormattedIds));
        $_objects = array();
        foreach($_objectsFromCache as $_memcachedKey => &$_objectFromCache) {
            if($_objectFromCache->version == $_objectFromCache::VERSION) {
                $_objects[$_memcachedFormattedIds[$_memcachedKey]] = $_objectFromCache;
            }
        }
        return $_objects;
    }

    /**
     * @param array $vos
     * @return boolean
     */
    protected function _saveMulti(array $vos = array()) {
        if (!$this->memcached) {
            return true;
        }

        $toSave = array();
        foreach($vos as $vo) {
            $toSave[$this->getMemcachedKey($vo->id)] = $vo;
        }
        return $this->memcached->setMulti($toSave);
    }

    protected function _insert(\Berthe\AbstractVO &$vo) {
        if (!$this->memcached) {
            return true;
        }

        return $this->memcached->set($this->getMemcachedKey($vo->id), $vo);
    }

    protected function _update(\Berthe\AbstractVO &$vo) {
        if (!$this->memcached) {
            return true;
        }

        return $this->_insert($vo);
    }

    protected function _delete(\Berthe\AbstractVO &$vo) {
        if (!$this->memcached) {
            return true;
        }

        return $this->memcached->delete($this->getMemcachedKey($vo->id));
    }
}