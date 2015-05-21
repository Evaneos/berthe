<?php

namespace Berthe\DAL;

interface DbWriter extends Connection
{
    public function beginTransaction();

    public function commit();

    public function lastInsertId($tableName = null, $primaryKey = null);

    public function rollBack();

    public function delete($table, $where = '');

    public function update($table, array $bind, $where = '');

    public function insert($table, array $bind);
}
