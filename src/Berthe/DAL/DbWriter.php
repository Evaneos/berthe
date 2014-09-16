<?php

namespace Berthe\DAL;

class DbWriter extends DbReader
{

    /**
     * Execute statement
     * @param string $sql
     * @param array $values array of key => value
     * @return boolean
     */
    public function query($sql, array $values = array()) {
        // retro-compatibility
        if (empty($values) || isset($values[0])) {
            $binds = $values;
        } else {
            $result = $this->transformToBinds($values);
            $sql = vsprintf($sql, $result['sprintfArgs']);
            $binds = $result['binds'];
        }
        $sanitizedBinds = $this->sanitizeBinds($binds);
        return $this->db->query($sql, $sanitizedBinds);
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
