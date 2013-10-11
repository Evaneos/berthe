<?php

class Evaneos_Berthe_Memcached extends Memcached {

    /**
     *
     * @var boolean|string
     * @access private
     */
    private $_baseKey = false;

    /**
     *
     * @var array
     * @access private
     */
    private $_origKeys = array();

    /**
     *
     * @var array
     * @access private
     */
    private $_translatedKeys = array();

    /**
     *
     * @var Memcached
     */
    private $_memcached = null;
    private $_noCache = false;


    private static $_memcachedInstance = null;

    /*
     * @return Evaneos_Berthe_Memcached
     */
    function __construct() {
        if (!self::$_memcachedInstance) {
            $this->_memcached = new Memcached();
            $this->_memcached->addServer('127.0.0.1', 11211);
            if (Zend_Registry::get('config')->debug->memcached->flush) {
                $this->_memcached->flush();
            }
            
            self::$_memcachedInstance = $this->_memcached;
        }
        else {
            $this->_memcached = self::$_memcachedInstance;
        }
        if (Zend_Registry::get('config')->debug->memcached->no_cache) {
            $this->_noCache = true;
        }

        $this->prepareMemcachedNamespaceKey();
    }

    /**
     *
     * @return type 
     */
    private function prepareMemcachedNamespaceKey() {
        if (strstr($_SERVER['HTTP_HOST'], '.dev.evaneos.com')) {
            $this->_baseKey = $_SERVER['HTTP_HOST'] . ':';
        }
    }

    public function __call($name, $arguments) {
        $_ret = $this->_memcached->{$name}($arguments);
        return $_ret;
    }

    /**
     *
     * @param type $key
     * @return type 
     */
    public function get($key, $cache_cb = null, &$cas_token = null) {
        if($this->_noCache) {
            return null;
        }
        $_ret = $this->_memcached->get($key);
        return $_ret;
    }

    /**
     * 
     */
    public function set($key, $value, $expiration = 0) {
        $_ret = $this->_memcached->set($key, $value, $expiration);
        return $_ret;
    }

    /**
     * 
     */
    public function getMulti(array $keys, &$cas_tokens = null, $flags = null) {
        if($this->_noCache) {
            return array();
        }

//        $_test = ($this->_baseKey !== false);

//        if ($_test) {
//            $keys = array_flip($keys);
//            $keys = $this->_translateKeys($keys);
//            $keys = array_keys($keys);
//        }

        $_ret = $this->_memcached->getMulti($keys, $cas_tokens, $flags);

        if (!empty($_ret)) {
            $_ret = $this->_reverttranslation($_ret);
        }
        return $_ret;
    }

    /**
     *
     * @param array $items
     * @param integer $time
     * @return boolean
     */
    public function setMulti(array $items, $time = 0) {
//        $_test = ($this->_baseKey !== false and is_array($items));

//        if ($_test) {
//            $this->_translateKeys($items);
//        }
        $_ret = $this->_memcached->setMulti($items, $time);
        return $_ret;
    }

    /**
     * Flush the cache
     * 
     * @link http://php.net/manual/en/memcached.flush.php
     * @see Memcached::flush()
     * @param integer $delay
     * @return boolean
     */
    public function flush($delay = 0) {
        $_ret = $this->_memcached->flush($delay);
        return $_ret;
    }

    /**
     *
     * @param array $keys
     * @return array 
     */
    private function _translateKeys($keys) {
        // resetting data
        $this->_translatedKeys = array();
        $this->_origKeys = array();

        foreach ($keys as $key => $value) {
            $newKey = $this->_baseKey . $key;
            $this->_translatedKeys[$newKey] = $key;
            $this->_origKeys[$newKey] = $value;
        }
        return $this->_origKeys;
    }

    /**
     *
     * @param array $infos
     * @return array
     */
    private function _reverttranslation($infos) {
        $_ret = array();

        foreach ($infos as $key => $value) {
            if (isset($this->_translatedKeys[$key])) {
                $_ret[$this->_translatedKeys[$key]] = $value;
            }
        }
        // resetting data
        $this->_translatedKeys = array();
        $this->_origKeys = array();
        return $_ret;
    }

    /**
     * @return int
     */
    public function getResultCode() {
        return $this->_memcached->getResultCode();
    }

    /**
     * @return bool 
     */
    public function delete($key, $time = 0) {
        return $this->_memcached->delete($key, $time);
    }
}