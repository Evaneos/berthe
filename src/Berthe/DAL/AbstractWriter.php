<?php

namespace Berthe\DAL;

abstract class AbstractWriter implements Writer
{

    const DEFAULT_TABLE_NAME = 'Berthe\DAL\AbstractWriter\UnsetTableName';

    /**
     * @var DbWriter
     */
    protected $db = null;

    /**
     * Name of the DB schema containing the table
     * @var string
     */
    protected $schemaName = '';

    protected $tableName = self::DEFAULT_TABLE_NAME;

    protected $identityColumn = 'id';

    protected $escapeCharacter = '"';

    protected $deleteColumnName = null;

    protected $translator = null;

    public function __construct()
    {
        $this->translator = new NullTranslator();
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * Set the Db Writer
     *
     * @param DbWriter $db
     */
    public function setDb(DbWriter $db) {
        $this->db = $db;

        return $this;
    }

    /**
     * Sets the escape character for field and table names in queries
     * @param string $char
     * @return \Berthe\DAL\AbstractWriter
     */
    public function setEscapeCharacter($char)
    {
        $this->escapeCharacter = $char;

        return $this;
    }

    /**
     * Set the identity column name
     *
     * @param string $columnName
     */
    public function setIdentityColumn($columnName)
    {
        $this->identityColumn = $columnName;

        return $this;
    }

    public function setSchema($schemaName)
    {
        $this->schemaName = $schemaName;

        return $this;
    }

    /**
     * Set the table name
     *
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Set the soft delete column name
     *
     * @param string $deleteColumnName
     */
    public function setSoftDelete(string $deleteColumnName)
    {
        if (!is_string($deleteColumnName)) {
             throw new InvalidArgumentException('setSoftDelete only accepts string as argument.');
        }
        $this->deleteColumnName = $deleteColumnName;
        return $this;
    }


    protected function validateTableAndIdentityColumn()
    {
        if ($this->tableName == self::DEFAULT_TABLE_NAME || trim($this->tableName) == '') {
            throw new \RuntimeException('Table name is not set !');
        }

        if (trim($this->identityColumn) == '') {
            throw new \RuntimeException('Identity column name cannot be null.');
        }
    }

    protected function getTableName()
    {
        $tableName = '';

        if (trim($this->schemaName) !== '') {
            $tableName = $this->escapeCharacter . $this->schemaName . $this->escapeCharacter . '.';
        }

        $tableName .= $this->escapeCharacter . $this->tableName . $this->escapeCharacter;

        return $tableName;
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

        $valueElements = array();
        $columnElements = array();
        $params = array();

        $values = $object->__toArray();

        foreach ($mappings as $column => $property) {
            if ($column != $this->identityColumn) {
                $value = in_array($property, $object->getTranslatableFields()) ?
                    $object->getAttribute($property) :
                    $values[$property];
                $columnElements[] = $column;
                $params[$column] = $value;

                if ($value instanceof \Berthe\Translation\Translation) {
                    $this->translator->saveTranslation($value);
                }
            }
        }

        $columnAssignment = $this->escapeCharacter .
            implode($this->escapeCharacter . ', ' . $this->escapeCharacter, $columnElements) .
            $this->escapeCharacter;
        $valueAssignment = '%s' . str_repeat(', %s', count($columnElements) -1);

        $query = <<<EOQ
INSERT INTO {$this->getTableName()} ({$columnAssignment})
VALUES ({$valueAssignment});
EOQ;

        if ((bool)$this->db->query($query, $params)) {
            $id = (int)$this->db->lastInsertId($this->getTableName(), $this->identityColumn);
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
            $escape = $this->escapeCharacter;
            $clauseElements[] = sprintf('%s%s%s = %%s', $escape, $column, $escape);
            if (in_array($property, $object->getTranslatableFields())) {
                $value = $object->getAttribute($property);
            } else {
                $value = $values[$property];
            }
            $params[$column] = $value;

            if ($value instanceof \Berthe\Translation\Translation) {
                $this->translator->saveTranslation($value);
            }
        }

        $params['identity'] = $object->getId();

        $columnAssignment = implode(', ', $clauseElements);

        $query = <<<EOQ
UPDATE {$this->getTableName()} SET {$columnAssignment}
WHERE {$this->escapeCharacter}{$this->identityColumn}{$this->escapeCharacter} = %s;
EOQ;

        return (bool)$this->db->query($query, $params);
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
        // If the soft delete is activated
        if ($this->deleteColumnName) {
            $query = <<<EOQ
UPDATE {$this->getTableName()}
SET {$this->deleteColumnName} = 1
WHERE {$this->escapeCharacter}{$this->identityColumn}{$this->escapeCharacter} = %s
EOQ;
        } else {
            $query = <<<EOQ
DELETE FROM {$this->getTableName()}
WHERE {$this->escapeCharacter}{$this->identityColumn}{$this->escapeCharacter} = %s
EOQ;
        }

        $params = array('identity' => $id);

        return (bool)$this->db->query($query, $params);
    }

    protected function getDefaultMappings(\Berthe\VO $vo) {
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
