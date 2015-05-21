<?php

namespace Berthe\DAL;

use Berthe\Util\ParameterTransformer;

class DefaultDbReader extends DbAdapter implements DbReader
{
    /**
     * @var ParameterTransformer
     */
    protected $parameterTransformer;

    /**
     * @param \Zend_Db_Adapter_Abstract $db
     * @param ParameterTransformer      $parameterTransformer
     */
    public function __construct(\Zend_Db_Adapter_Abstract $db, ParameterTransformer $parameterTransformer)
    {
        $this->parameterTransformer = $parameterTransformer;
        parent::__construct($db);
    }

    /**
     * @param string      $sql
     * @param array $bind
     *
     * @return \Zend_Db_Statement_Interface
     */
    public function query($sql, $bind = array())
    {
        return $this->getAdapter()->query($sql, $bind);
    }

    /**
     * @return int
     */
    public function getFetchMode()
    {
        return $this->getAdapter()->getFetchMode();
    }

    /**
     * @param string $query
     * @param array  $parameters
     * @param string $fetchMode
     *
     * @return array
     */
    public function fetchAll($query, array $parameters = array(), $fetchMode = null)
    {
        $parameters = $this->parameterTransformer->transform($parameters);

        return $this->db->fetchAll($query, $parameters, $fetchMode);
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return mixed
     */
    public function fetchOne($query, array $parameters = array())
    {
        $parameters = $this->parameterTransformer->transform($parameters);
        return $this->db->fetchOne($query, $parameters);
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    public function fetchAssoc($query, array $parameters = array())
    {
        $parameters = $this->parameterTransformer->transform($parameters);
        return $this->db->fetchAssoc($query, $parameters);
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    public function fetchCol($query, array $parameters = array())
    {
        $parameters = $this->parameterTransformer->transform($parameters);
        return $this->db->fetchCol($query, $parameters);
    }

    /**
     * @param string $query
     * @param array  $parameters
     *
     * @return array
     */
    public function fetchPairs($query, array $parameters = array())
    {
        $parameters = $this->parameterTransformer->transform($parameters);
        return $this->db->fetchPairs($query, $parameters);
    }

    /**
     *
     * @param string $query
     * @param array  $parameters
     * @param int    $fetchMode
     *
     * @return array
     */
    public function fetchRow($query, array $parameters = array(), $fetchMode = null)
    {
        $parameters = $this->parameterTransformer->transform($parameters);
        return $this->db->fetchRow($query, $parameters, $fetchMode);
    }

    /**
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        return $this->db->describeTable($tableName, $schemaName);
    }
}
