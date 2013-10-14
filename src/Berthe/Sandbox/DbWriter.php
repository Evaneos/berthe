<?php

class Berthe_Sandbox_DbWriter extends Berthe_DbWriter {
    /**
     * Execute statement
     * @param string $sql
     * @param array $bind
     * @return boolean
     */
    public function query($sql, array $bind = array()) {
        return true;
    }
    
    /**
     * Retrieve the last insert id for table & given pk
     * @param string $tableName
     * @param string $primaryKey
     * @return int 
     */
    public function lastInsertId($tableName = null, $primaryKey = null) {
        return rand();
    }
}