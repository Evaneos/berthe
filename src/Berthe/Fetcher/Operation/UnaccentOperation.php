<?php

namespace Berthe\Fetcher\Operation;

use Berthe\Fetcher;

class UnaccentOperation implements OperationValue
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $fetcherType;

    /**
     * @param int $fetcherType
     * @param string $value
     * @throws \RuntimeException
     */
    public function __construct($fetcherType, $value)
    {
        switch ($fetcherType) {
            case Fetcher::TYPE_DIFF:
            case Fetcher::TYPE_EQ:
            case Fetcher::TYPE_ILIKE:
            case Fetcher::TYPE_LIKE:
                $this->fetcherType = $fetcherType;
                break;
            default:
                throw new \RuntimeException(sprintf('Unsupported Fetcher type %s for Unaccent operation', $fetcherType));
        }
        $this->value = $value;
    }

    /**
     * @param string $fieldName
     * @return array
     */
    public function getOperationValue($fieldName)
    {
        return array(
            sprintf('unaccent(%s) %s unaccent(?)', $fieldName, self::strFilterToDbNotation($this->fetcherType)),
            array($this->value)
        );
    }

    /**
     * @param $fetcherType
     * @return string
     */
    private static function strFilterToDbNotation($fetcherType)
    {
        switch ($fetcherType) {
            case Fetcher::TYPE_DIFF:    return '!=';
            case Fetcher::TYPE_EQ:      return '=';
            case Fetcher::TYPE_ILIKE:   return 'ILIKE';
            case Fetcher::TYPE_LIKE:    return 'LIKE';
        }
    }
}
