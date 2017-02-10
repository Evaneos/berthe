<?php

namespace Berthe\Fetcher\Operation;

use Berthe\Fetcher;

/**
 * Class ColumnOperation
 *
 * @package Berthe\Fetcher\Operation
 **/
class ColumnOperation implements OperationValue
{
    /**
     * The column to compare
     *
     * @var string
     */
    private $columnToCompare;

    /**
     * The fetcher type
     *
     * @var int
     */
    private $fetcherType;

    /**
     * @var array
     */
    private static $allowedFetcherTypes = [
        Fetcher::TYPE_EQ,
        Fetcher::TYPE_DIFF,
    ];

    /**
     * @param int $fetcherType
     * @param string $columnToCompare
     */
    public function __construct($fetcherType, $columnToCompare)
    {
        $this->setFetcherType($fetcherType);
        $this->columnToCompare = $columnToCompare;
    }

    /**
     * @param string $fieldName
     *
     * @return array array of query and params
     */
    public function getOperationValue($fieldName)
    {
        $sql = sprintf(
            '%s %s %s',
            $fieldName,
            $this->strFilterToDbNotation(),
            $this->columnToCompare
        );

        return [
            $sql,
            [],
        ];
    }

    /**
     * @param string $fetcherType
     */
    private function setFetcherType($fetcherType)
    {
        if (!in_array($fetcherType, ColumnOperation::$allowedFetcherTypes)) {
            throw new \RuntimeException(sprintf('Unsupported Fetcher type %s for Column operation', $fetcherType));
        }
        $this->fetcherType = $fetcherType;
    }

    /**
     * Get the SQL operator related to the fetcher
     *
     * @return string
     */
    private function strFilterToDbNotation()
    {
        switch ($this->fetcherType) {
            case Fetcher::TYPE_DIFF:
                return '!=';
            case Fetcher::TYPE_EQ:
                return '=';
        }
    }
}
