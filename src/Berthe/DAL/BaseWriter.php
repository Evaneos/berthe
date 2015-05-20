<?php

namespace Berthe\DAL;

class BaseWriter extends AbstractWriter implements Writer
{

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    public function setIdentityColumn($column)
    {
        $this->identityColumn = $column;

        return $this;
    }
}
