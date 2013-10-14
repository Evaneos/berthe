<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Blacklist
 *
 * @author tony
 */
class Berthe_Blacklist {
    /**
     *
     * @var Berthe_Blacklist
     */
    protected static $_instance = null;
    
    protected $_manager = null;
    
    protected $_ignoreBlacklist = true;
    
    /**
     * 
     * @return Berthe_Blacklist
     */
    public static function getInstance() {
        is_null(self::$_instance) and self::$_instance = new self();
        return self::$_instance;
    }
    
    /**
     * Constructor
     */
    protected function __construct() {
        $_context = Berthe_Context::getInstance();
        $_mf = new Berthe_FactoryManager();
        $_mf->setContext($_context);
        $this->_manager = $_mf->getInstanceBlacklist();
    }
    
    /**
     * 
     * @param string $email
     * @return boolean
     */
    public function isEmailBlacklisted($email) {
        if($this->_ignoreBlacklist) {
            return false;
        } else {
            return $this->_manager->isEmailBlacklisted($email);
        }
    }
    
    /**
     * If call the blacklist will be ignored (method isEmailBlacklisted will always return false)
     */
    public function ignoreBlacklist() {
        $this->_ignoreBlacklist = true;
    }
    
    /**
     * If call the blacklist will be used (method isEmailBlacklisted will return true if the email is blacklisted)
     */
    public function useBlacklist() {
        $this->_ignoreBlacklist = false;
    }
    
    /**
     * Know if blacklist is ignored or not
     * @return boolean
     */
    public function isBlacklistIgnored() {
        return $this->_ignoreBlacklist;
    }
}

?>
