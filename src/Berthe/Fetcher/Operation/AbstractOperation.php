<?php
namespace Berthe\Fetcher\Operation;

use Berthe\Fetcher\FetcherOperation;

abstract class AbstractOperation implements FetcherOperation
{
    
    /**
     * The operator to use
     *
     * @var mixed
     */
    protected $operator;
    
    /**
     * The group name
     *
     * @var string
     */
    protected $groupName;
    
    /**
     *
     * @param mixed $operator
     * @param string $groupName
     */
    public function __construct($operator = null, $groupName = null)
    {
        $this->operator = $operator;
        $this->groupName = $groupName;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Fetcher\FetcherOperation::setOperator()
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Fetcher\FetcherOperation::getOperator()
     */
    public function getOperator()
    {
        return $this->operator;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Fetcher\FetcherOperation::setGroupName()
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Berthe\Fetcher\FetcherOperation::getGroupName()
     */
    public function getGroupName()
    {
        return $this->groupName;
    }
}
