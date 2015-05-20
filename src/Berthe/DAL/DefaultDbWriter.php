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
     * @param string $query
     * @param array $parameters
     */
    protected function prepare(&$query, array &$parameters = array())
    {
        if (empty($parameters) || isset($parameters[0])) {
            return;
        }

        $replaced = array();
        $binds = array();

        foreach ($parameters as $key => $value) {
            if ($value instanceof \Berthe\DAL\ComplexType) {
                list($query, $params) = $value->toDbRepresentation();
                $args = array();
                foreach ($params as $paramKey => $paramValue) {
                    $paramName = $key . '_' . $paramKey;
                    $args[] = ':' . $paramName;
                    $binds[':' . $paramName] = $paramValue;
                }
                $replaced[] = vsprintf($query, $args);
            } else {
                $replaced[] = ':' . $key;
                $binds[':' . $key] = $value;
            }
        }

        $query = vsprintf($query, $replaced);
        $parameters = $this->parametersTransformer->transform($parameters);
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
     * @param string $query
     * @param array  $parameters
     *
     * @return boolean
     */
    public function query($query, array $parameters = array())
    {
        $this->prepare($query, $parameters);
        return $this->db->query($query, $parameters);
    }
}
