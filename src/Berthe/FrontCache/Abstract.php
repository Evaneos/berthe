<?php
abstract class Evaneos_Berthe_FrontCache_Abstract {
    /**
     * @return static
     */
    public static function getInstance() {
        static::$instance === null && static::$instance = new static();
        return static::$instance;
    }
    
    protected function __construct() {}
    protected function __clone(){}
    
    /**
     * @param array $urls
     * @return boolean
     */
    public function purgeURLs(array $urls = array()) {
        foreach($urls as $url) {
            $this->_purgeURL($url);
        }
        
        return true;
    }
    
    /**
     * @param string $url
     * @return boolean
     */
    public function purgeURL($url) {
        try {
            $this->_purgeURL($url);
        }
        catch (Exception $e) {
            trigger_error('Couldnt PURGE url, reason = "' . $e->getMessage() . '"', E_USER_NOTICE);
        }
        
        return true;
    }
    
    /**
     * 
     */
    abstract protected function _purgeURL($url);
}