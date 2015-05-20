<?php

namespace Berthe\DAL;

use Berthe\VO;

class StoreMemcached extends AbstractStore
{
    const DEFAULT_PREFIX = 'berthe';
    const DEFAULT_SEPARATOR = ':';

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
    protected $prefix = self::DEFAULT_PREFIX;
    /**
     * @var string
     */
    protected $name = null;
    /**
     * @var string
     */
    protected $suffix = null;
    /**
     * @var string
     */
    protected $separator = self::DEFAULT_SEPARATOR;

    public function __construct(\Memcached $memcached)
    {
        $this->setMemcachedInstance($memcached);
    }

    public function setMemcachedInstance(\Memcached $memcached)
    {
        $this->memcached = $memcached;
        return $this;
    }

    public function setPrefix($value)
    {
        $this->prefix = $value;
        return $this;
    }

    public function setSuffix($value)
    {
        $this->suffix = $value;
        return $this;
    }

    public function setName($value)
    {
        $this->name = $value;
        return $this;
    }

    /**
     * Appends the suffix to the base memcached key
     * @param int $id
     * @return string
     */
    public function getMemcachedCompleteKey($id)
    {
        $memcachedKey = implode($this->separator, array($this->prefix, $this->name, $this->suffix, $id));
        return $memcachedKey;
    }

    /**
     * @param array $ids
     * @return array id=>object
     */
    protected function _load(array $ids = array())
    {
        if (!$this->memcached) {
            return array();
        }

        $_idsCopy = array_flip($ids);

        // Array transformation :  array[id] = id  INTO  array[memcachedId] = id
        array_walk($_idsCopy, function (&$value, $key, $context) {
            $value = $context->getMemcachedCompleteKey($key); // "ev:mc:obj:" . $value;
        }, $this);
        $_memcachedFormattedIds = array_flip($_idsCopy);

        // unserialized & store locally
        $_objectsFromCache = $this->memcached->getMulti(array_keys($_memcachedFormattedIds));
        $_objects = array();
        foreach ($_objectsFromCache as $_memcachedKey => &$_objectFromCache) {
            if ($_objectFromCache->getVersion() == $_objectFromCache::VERSION) {
                $_objects[$_memcachedFormattedIds[$_memcachedKey]] = $_objectFromCache;
            }
        }
        return $_objects;
    }

    /**
     * @param array $vos
     * @return boolean
     */
    protected function _saveMulti(array $vos = array())
    {
        if (!$this->memcached) {
            return true;
        }

        $toSave = array();
        foreach ($vos as $vo) {
            $toSave[$this->getMemcachedCompleteKey($vo->getId())] = $vo;
        }
        return $this->memcached->setMulti($toSave);
    }

    protected function _insert(VO $vo)
    {
        if (!$this->memcached) {
            return true;
        }

        return $this->memcached->set($this->getMemcachedCompleteKey($vo->getId()), $vo);
    }

    protected function _update(VO $vo)
    {
        if (!$this->memcached) {
            return true;
        }

        return $this->_insert($vo);
    }

    protected function _delete(VO $vo)
    {
        if (!$this->memcached) {
            return true;
        }

        return $this->memcached->delete($this->getMemcachedCompleteKey($vo->getId()));
    }
}
