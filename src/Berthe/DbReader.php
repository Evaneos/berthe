<?php
/**
 * Class definition for Berthe abstract Manager Berthe_DbReader
 * 
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Berthe/DbReader.php
 * @package Berthe
 */
class Berthe_DbReader extends Berthe_DbAdapter {
    /**
     * @param string $sql
     * @param array $array
     * @param string $fetchMode
     * @return array 
     */
    public function fetchAll($sql, array $array = array(), $fetchMode = null) {
        $this->log($sql, $array);
        return $this->db->fetchAll($sql, $array, $fetchMode);
    }
    
    /**
     *
     * @param string $sql
     * @param array $array
     * @return mixed 
     */
    public function fetchOne($sql, array $array = array()) {
        $this->log($sql, $array);
        return $this->db->fetchOne($sql, $array);
    }
    
    /**
     *
     * @param string $sql
     * @param array $array
     * @return array 
     */
    public function fetchAssoc($sql, array $array = array()) {
        $this->log($sql, $array);
        return $this->db->fetchAssoc($sql, $array);
    }
    
    /**
     *
     * @param string $sql
     * @param array $array
     * @return array 
     */
    public function fetchCol($sql, array $array = array()) {
        $this->log($sql, $array);
        return $this->db->fetchCol($sql, $array);
    }
    
    /**
     *
     * @param string $sql
     * @param array $array
     * @return array 
     */
    public function fetchPairs($sql, array $array = array()) {
        $this->log($sql, $array);
        return $this->db->fetchPairs($sql, $array);
    }
    
    /**
     *
     * @param string $sql
     * @param array $array
     * @param int $fetchMode
     * @return array 
     */
    public function fetchRow($sql, array $array = array(), $fetchMode = null) {
        $this->log($sql, $array);
        return $this->db->fetchRow($sql, $array, $fetchMode);
    }
    
    /**
     *
     * @param string $tableName
     * @param string $schemaName
     * @return array 
     */
    public function describeTable($tableName, $schemaName = null) {
        return $this->db->describeTable($tableName, $schemaName);
    }
}
