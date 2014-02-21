<?php

namespace Berthe\DAL;

abstract class AbstractWriter implements Writer {

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
     * @param \Berthe\VO $object the object to insert
     * @return boolean
     */
    public function insert(\Berthe\VO $object) {
        $this->validateTableAndIdentityColumn();

        $mappings = $this->getSaveMappings();

        if (empty($mappings)) {
            $mappings = $this->getDefaultMappings($object);
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
VALUES ({$valueAssignment});
EOQ;

        if ((bool) $this->db->query($query, $params)) {
            $id = (int) $this->db->lastInsertId($this->tableName, $this->identityColumn);
            $object->setId($id);
        }

        return $object->getId() > 0;
    }

    /**
     * Update the object in database
     * @param \Berthe\VO $object the object to insert
     * @return boolean
     */
    public function update(\Berthe\VO $object) {
        $this->validateTableAndIdentityColumn();

        $mappings = $this->getSaveMappings();

        if (empty($mappings)) {
            $mappings = $this->getDefaultMappings($object);
        }

        $clauseElements = array();
        $params = array();

        $values = $object->__toArray();

        foreach ($mappings as $column => $property) {
            $value = $values[$property];
            $clauseElements[] = sprintf('%s = :%s', $column, $column);
            $params[':' . $column] = $value;
        }

        $params[':identity'] = $object->getId();

        $columnAssignment = implode(', ', $clauseElements);

        $query = <<<EOQ
UPDATE {$this->tableName} SET {$columnAssignment}
WHERE {$this->identityColumn} = :identity;
EOQ;

        return (bool) $this->db->query($query, $params);
    }

    /**
     * Delete the object from database
     * @param \Berthe\VO $object the object to insert
     * @return boolean
     */
    public function delete(\Berthe\VO $object) {
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

    private function getDefaultMappings(\Berthe\VO $vo) {
        $properties = array_keys($vo->__toArray());

        $mappings = array_combine($properties, $properties);

        return array_filter($mappings, function($value) {
            return ! ($value == 'id' || $value == 'version');
        });
    }

    protected function getSaveMappings() {
        return array();
    }
}