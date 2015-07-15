<?php

namespace Berthe\DAL;

use Berthe\Util\ParameterTransformer;

class DefaultDbWriter extends DbAdapter implements DbWriter
{
    /**
     * @var ParameterTransformer
     */
    protected $parametersTransformer;

    /**
     * @param ParameterTransformer $parameterTransformer
     */
    public function __construct(\Zend_Db_Adapter_Abstract $db, ParameterTransformer $parameterTransformer)
    {
        $this->parametersTransformer = $parameterTransformer;
        parent::__construct($db);
    }

    /**
     * @param string $table
     * @param string $where
     *
     * @return int
     */
    public function delete($table, $where = '')
    {
        return $this->getAdapter()->delete($table, $where);
    }

    /**
     * @param string $table
     * @param array  $bind
     * @param string $where
     *
     * @return mixed
     */
    public function update($table, array $bind, $where = '')
    {
        return $this->getAdapter()->update($table, $bind, $where);
    }

    /**
     * @param string      $table
     * @param array $bind
     *
     * @return int
     * @throws \Zend_Db_Adapter_Exception
     */
    public function insert($table, array $bind)
    {
        return $this->getAdapter()->insert($table, $bind);
    }


    /**
     * @param string $query
     * @param array $parameters
     */
    protected function prepare(&$query, array &$parameters = array())
    {
        $binds = array();
        $sprintfArgs = array();

        foreach ($parameters as $key => $value) {
            if ($value instanceof \Berthe\DAL\ComplexType) {
                list($subQuery, $params) = $value->toDbRepresentation();
                $args = array();

                foreach ($params as $paramKey => $paramValue) {
                    $paramName = $key . '_' . $paramKey;
                    $args[] = ':' . $paramName;
                    $binds[':' . $paramName] = $paramValue;
                }
                $sprintfArgs[] = vsprintf($subQuery, $args);
            } else {
                $sprintfArgs[] = ':' . $key;
                $binds[':' . $key] = $value;
            }
        }

        if (empty($parameters) || array_key_exists(0, $parameters)) {
            $binds = $parameters;
        } else {
            $query = vsprintf($query, $sprintfArgs);
        }

        $parameters = $this->parametersTransformer->transform($binds);
    }

    /**
     * @param string $tableName
     * @param string $primaryKey
     *
     * @return int
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        return $this->db->lastInsertId($tableName, $primaryKey);
    }

    /**
     * @return boolean
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * @return boolean
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * @return boolean
     */
    public function rollback()
    {
        return $this->db->rollBack();
    }

    /**
     * @param string      $sql
     * @param array $bind
     *
     * @return \Zend_Db_Statement_Interface
     */
    public function query($sql, $bind = array())
    {
        $this->prepare($sql, $bind);

        return $this->db->query($sql, $bind);
    }
}
