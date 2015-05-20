<?php

namespace Berthe\DAL;

interface DbWriter
{
    /**
     * @param string $query
     * @param array $parameters
     *
     * @return boolean
     */
    public function query($query, array $parameters = array());

    /**
     * @param string $tableName
     * @param string $primaryKey
     *
     * @return int
     */
    public function lastInsertId($tableName = null, $primaryKey = null);

    /**
     * @return boolean
     */
    public function beginTransaction();

    /**
     * @return boolean
     */
    public function commit();

    /**
     * @return boolean
     */
    public function rollback();
}
