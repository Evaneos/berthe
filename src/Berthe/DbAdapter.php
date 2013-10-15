<?php
/**
 * Class definition for Berthe abstract Manager Berthe_DbAdapter
 * 
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Berthe/DbAdapter.php
 * @package Berthe
 */
class Berthe_DbAdapter {
    /**
     * @var Log_Logger 
     */
    protected $logger = null;
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract 
     */
    protected $db = null;
    
    public function __construct(Zend_Db_Adapter_Abstract $db) {
        $this->db = $db;
        $this->logger = new Log_Logger();
        $this->logger->addWriter(new Log_FileWriter(ROOT_DIR . '/tmp/db.log'));
        $isLoggable = Zend_Registry::get(Initializer::CONFIG)->database->logger;
        $this->logger->isEnabled($isLoggable);
    }
    
    /**
     *
     * @return Zend_Db_Adapter_Abstract 
     */
    public function getAdapter() {
        return $this->db;
    }
    
    /**
     * @param string $sql
     * @param array $binds
     */
    protected function log($sql, array $binds = array()) {
        if (count($binds) > 0) {
            $this->logger->log("--------------------\n" . $sql . "\n" . "With " . count($binds) . " params : " . "\n" . implode("\n", $binds));
        }
        else {
            $this->logger->log("--------------------\n" . $sql . "\n" . "Without params");
        }
    }
}