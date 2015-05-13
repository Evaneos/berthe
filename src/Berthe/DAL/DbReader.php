<?php

namespace Berthe\DAL;

interface DbReader
{
    /**
     * @param string $query
     * @param array $parameters
     * @param string $fetchMode
     *
     * @return array
     */
    public function fetchAll($query, array $parameters = array(), $fetchMode = null);

    /**
     * @param string $query
     * @param array $parameters
     *
     * @return mixed
     */
    public function fetchOne($query, array $parameters = array());

    /**
     * @param string      $query
     * @param array $parameters
     *
     * @return array
     */
    public function fetchAssoc($query, array $parameters = array());

    /**
     * @param string $query
     * @param array $parameters
     *
     * @return array
     */
    public function fetchCol($query, array $parameters = array());

    /**
     * @param string $query
     * @param array $parameters
     *
     * @return array
     */
    public function fetchPairs($query, array $parameters = array());

    /**
     *
     * @param string $query
     * @param array $parameters
     * @param int $fetchMode
     *
     * @return array
     */
    public function fetchRow($query, array $parameters = array(), $fetchMode = null);

    /**
     * @param string $tableName
     * @param string $schemaName
     * @return array
     */
    public function describeTable($tableName, $schemaName = null);
}
