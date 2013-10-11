<?php
/**
 * Class definition for Berthe abstract Manager Evaneos_Berthe_AbstractService
 * 
 * @author anthony@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/AbstractService.php
 * @package Evaneos/Berthe
 * @since Berthe
 */
abstract class Evaneos_Berthe_AbstractService {
    /**
     * @var Berthe_FactoryManager
     */
    public $managerFactory = null;
    /**
     * @var Berthe_FactoryService
     */
    public $serviceFactory = null;
    /**
     * @var Berthe_Context
     */
    public $context = null;
    /**
     * @var Evaneos_Berthe_DbWriter
     */
    protected $_db = null;
   
    /**
     * @return Evaneos_Berthe_DbWriter
     */
    public function getDb() {
        if (!$this->_db) {
            $this->_db = Zend_Registry::get("dbWriter");
        }
        
        return $this->_db;
    }
    
    /**
     * Determines if $methodName method is actually accessible by user
     * @param string $methodName The of the method to test
     * @return boolean 
     */
    public function isMethodAccessible($methodName){
        return true;
    }
}