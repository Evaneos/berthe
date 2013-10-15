<?php
class Berthe_FrontCache_Reloader {
    const PREFIX_RELOADER = 'FCR:RL:'; 
    
    protected static $instance = null;
    protected $isEnabled = false;
    /**
     * @return Berthe_FrontCache_Reloader
     */
    public static function getInstance() {
        self::$instance === null && self::$instance = new static();
        return self::$instance;
    }
    
    /**
     * @param bool|null $isEnabled
     * @return bool
     */
    public function isEnabled($isEnabled = null) {
        if ($isEnabled !== null) {
            $this->isEnabled = (bool) $isEnabled;
        }
        return $this->isEnabled;
    }
    
    protected function __construct() {}
    protected function __clone() {}
    
    /**
     * Return the unique redis key of a given berthe VO
     * @param string $guid
     * @param Berthe_AbstractVO $object
     * @return string
     */
    protected function generateKey($guid, Berthe_AbstractVO $object) {
        return sprintf('%s%s:%d', self::PREFIX_RELOADER, $guid, $object->id);
    }
    
    /**
     * Add a relation between an URL and data (either a Berthe VO or an array of Berthe VO)
     * @param string $guid
     * @param Berthe_AbstractVO[] $data
     * @param string $url
     */
    public function addRelation($guid, $data, $url) {
        if (!$this->isEnabled()) {
            return;
        }
        
        // @DEV we don't log relation when we are logged because we would log and flush all site with user object (for example)
        if (Berthe_Context::getInstance()->isUserLogged()) {
            return;
        }
        
        $isOK = true;
        if (is_array($data)) {
            foreach($data as $object) {
                $ret = $this->_addRelation($guid, $object, $url);
                if (!$ret) {
                    $isOK = false;
                }
            }
        }
        elseif ($data instanceof Berthe_AbstractVO) {
            $isOK = $this->_addRelation($guid, $data, $url);
        }
        
        return $isOK;
    }
    
    /**
     * PURGE all the URLs of the $data (either a Berthe VO, or an array of Berthe VO)
     * @param string $guid
     * @param Berthe_AbstractVO[] $data
     * @return boolean
     */
    public function flushRelation($guid, $data) {
        if (!$this->isEnabled()) {
            return;
        }
        
        $db = Redis_Db::getInstance()->getDb();
        
        $isOK = true;
        if (is_array($data)) {
            foreach($data as $object) {
                $ret = $this->_flushRelation($guid, $object);
                if ($ret) {
                    $db->del($this->generateKey($guid, $object));
                }
                if (!$ret) {
                    $isOK = false;
                }
            }
        }
        elseif ($data instanceof Berthe_AbstractVO) {
            $isOK = $this->_flushRelation($guid, $data);
            if ($isOK) {
                $db->del($this->generateKey($guid, $data));
            }
        }
        
        return $isOK;
    }
    
    /**
     * Add a relation between an object and an url
     * @param string $guid
     * @param Berthe_AbstractVO $object
     * @param string $url
     * @return boolean
     */
    protected function _addRelation($guid, Berthe_AbstractVO $object, $url) {
        $db = Redis_Db::getInstance()->getDb();
        $key = $this->generateKey($guid, $object);
        NewRelic_PluginAPI::getInstance()->incrementMetric(Berthe_Util_NewRelic::getKey(Berthe_Util_NewRelic::PING_FRONTCACHE_ADDRELATION_COUNT));
        $db->sAdd($key, $url);
        return true;
    }
    
    /**
     * PURGE all URLs of a given object
     * @param string $guid
     * @param Berthe_AbstractVO $object
     * @return boolean
     */
    protected function _flushRelation($guid, Berthe_AbstractVO $object) {
        $db = Redis_Db::getInstance()->getDb();
        $key = $this->generateKey($guid, $object);

        $urls = $db->sMembers($key);
        if (is_array($urls)) {
            NewRelic_PluginAPI::getInstance()->incrementMetric(Berthe_Util_NewRelic::getKey(Berthe_Util_NewRelic::PING_FRONTCACHE_FLUSHRELATION_COUNT));
            Berthe_FrontCache_Varnish::getInstance()->purgeURLs($urls);
        }
        return true;
    }
}