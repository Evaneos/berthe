<?php
/**
 * Class definition for Berthe abstract Manager Berthe_DbWriter
 * 
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Berthe/DbWriter.php
 * @package Berthe
 */
class Berthe_DbWriter extends Berthe_DbReader {
    /**
     * Execute statement
     * @param string $sql
     * @param array $bind
     * @return boolean 
     */
    public function query($sql, array $bind = array()) {
        $sanitizedBinds = $this->sanitizeBinds($bind);
        $this->log($sql, $sanitizedBinds);
        return $this->db->query($sql, $sanitizedBinds);
    }
    
    /**
     * Sanitize binds
     * @param array $bind
     * @return array
     */
    private function sanitizeBinds(array $bind = array()) {
        $sanitizedBinds = array();
        foreach($bind as $key => $value) {
            switch(1) {
                case ($value instanceof DateTime) :
                    $sanitizedValue = $value->format('Y-m-d H:i:s');
                    break;
                case is_string($value) :
                    $sanitizedValue = mb_check_encoding($value, 'UTF-8') ? $value : utf8_encode($value);
                default :
                    $sanitizedValue = $value;
                    break;
            }
            $sanitizedBinds[$key] = $sanitizedValue;
        }
        return $sanitizedBinds;
    }
    
    /**
     * Retrieve the last insert id for table & given pk
     * @param string $tableName
     * @param string $primaryKey
     * @return int 
     */
    public function lastInsertId($tableName = null, $primaryKey = null) {
        return $this->db->lastInsertId($tableName, $primaryKey);
    }
    
    /**
     * Open transaction
     * @return boolean 
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     * @return boolean 
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     * @return boolean 
     */
    public function rollback() {
        return $this->db->rollback();
    }
}