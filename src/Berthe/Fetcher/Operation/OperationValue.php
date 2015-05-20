<?php
namespace Berthe\Fetcher\Operation;

interface OperationValue
{
    /**
     * @param string $fieldName
     * @return array array of query and params
     */
    public function getOperationValue($fieldName);
}
