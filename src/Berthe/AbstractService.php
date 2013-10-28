<?php
/**
 * Class definition for Berthe abstract Manager Berthe_AbstractService
 *
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0
 * @filesource Berthe/AbstractService.php
 * @package Berthe
 */
abstract class Berthe_AbstractService {
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
     * @var Berthe_DbWriter
     */
    protected $_db = null;

    /**
     * @return Berthe_DbWriter
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