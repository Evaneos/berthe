<?php

class Berthe_Sandbox_Memcached extends Berthe_Memcached {
    private $_data = array();
    
    /**
     *
     * @param type $key
     * @return type 
     */
    public function get($key, $cache_cb = null, $cas_token = null) {
        if($this->_noCache) {
            return null;
        }
        if ($this->_baseKey !== false) {
            $key = $this->_baseKey . $key;
        }

        $_ret = $this->_data[$key];

        return $_ret;
    }

    /**
     * 
     */
    public function set($key, $value, $expiration = 0) {

        if ($this->_baseKey !== false) {
            $key = $this->_baseKey . $key;
        }

        $this->_data[$key] = $value;

        return true;
    }

    /**
     * 
     */
    public function getMulti(array $keys, $cas_tokens = null, $flags = null) {
        if($this->_noCache) {
            return array();
        }

        $_test = ($this->_baseKey !== false);

        if ($_test) {
            $keys = array_flip($keys);
            $keys = $this->_translateKeys($keys);
        }

        $_ret = array_intersect_key($keys, $this->_data);

        if (!empty($_ret) and $_test) {
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

        $_test = ($this->_baseKey !== false and is_array($items));

        if ($_test) {
            $this->_translateKeys($items);
        }
        
        foreach ($items as $_key => $_value) {
            $this->_data[$_key] = $_value;
        }

        PROFILER and Profiler::endProfile($_dbg);
        return $_ret;
    }
}