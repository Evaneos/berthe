<?php

namespace Berthe\DAL;

abstract class AbstractWriter {
    
    const DEFAULT_TABLE_NAME = 'Berthe\DAL\AbstractWriter\UnsetTableName';
    
    /**
     * @var DbWriter
     */
    protected $db = null;
    
    protected $tableName = self::DEFAULT_TABLE_NAME;
    
    protected $identityColumn = 'id';

    public function setDb(DbWriter $db) {
        $this->db = $db;
        return $this;
    }
    
    private function validateTableAndIdentityColumn()
    {
        if ($this->tableName == self::DEFAULT_TABLE_NAME || trim($this->tableName) == '') {
            throw new \RuntimeException('Table name is not set !');
        }
        
        if (trim($this->identityColumn) == '') {
            throw new \RuntimeException('Identity column name cannot be null.');
        }
    }
    
    /**
     * Insert the object in database
     * @param \Berthe\AbstractVO $object the object to insert
     * @return boolean
     */
    public function insert(\Berthe\AbstractVO $object) {
        $this->validateTableAndIdentityColumn();
        
        $mappings = $this->getSaveMappings();
        
        if (empty($mappings)) {
            throw new \RuntimeException('Cannot use auto-generated queries with empty mappings.');
        }
        
        $columnElements = array();
        $valueElements = array();
        $params = array();
        
        $values = $object->__toArray();
        
        foreach ($mappings as $column => $property) {
            if ($column != $this->identityColumn) {
                $value = $values[$property];
                $columnElements[] = $column;
                $params[':' . $column] = $value;
            }
        }
        
        $columnAssignment = implode(', ', $columnElements);
        $valueAssignment = ':' . implode(', :', $columnElements);
        
        $query = <<<EOQ
            INSERT INTO {$this->tableName} ({$columnAssignment})
            VALUES ({$valueClause});
EOQ;
        
        if ((bool) $this->db->query($query, $params)) {
            $id = (int) $this->db->lastInsertId($this->tableName, $this->identityColumn);
            $object->setId($id);
        }
        
        return $object->getId() > 0;
    }
    
    /**
     * Update the object in database
     * @param \Berthe\AbstractVO $object the object to insert
     * @return boolean
     */
    public function update(\Berthe\AbstractVO $object) {
        $this->validateTableAndIdentityColumn();
        
        $mappings = $this->getSaveMappings();
        
        if (empty($mappings)) {
            throw new \RuntimeException('Cannot use auto-generated queries with empty mappings.');
        }
        
        $clauseElements = array();
        $params = array();
        
        $values = $object->__toArray();
        
        foreach ($mappings as $column => $property) {
            $value = $values[$property];
            $clauseElements[] = sprintf('%s = :%s', $column, $value);
            $params[':' . $column] = $value;
        }
        
        $columnAssignment = implode(', ', $clauseElements);
        $params[':' . $this->identityColumn] = $object->getId();
        
        $query = <<<EOQ
            UPDATE {$this->tableName} SET {$columnAssignment}
            WHERE {$this->identityColumn} = :identity;
EOQ;
            
        return (bool) $this->db->query($query, $params);
    }
    
    /**
     * Delete the object from database
     * @param \Berthe\AbstractVO $object the object to insert
     * @return boolean
     */
    public function delete(\Berthe\AbstractVO $object) {
        return $this->deleteById($object->getId());
    }
    
    /**
     * Delete an object by id from database
     * @param int $int object identifier
     * @return boolean
     */
    public function deleteById($id) {
        $this->validateTableAndIdentityColumn();
        
        $query = <<<EOQ
            DELETE FROM {$this->tableName} WHERE {$this->identityColumn} = :identity        
EOQ;
        
        $params = array(':identity' => $id);
        
        return (bool) $this->db->query($query, $params);
    }
    
    protected function getSaveMappings() {
        return array();
    }
}