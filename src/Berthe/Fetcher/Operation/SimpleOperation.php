<?php
namespace Berthe\Fetcher\Operation;

use Berthe\Fetcher;

class SimpleOperation extends AbstractOperation
{
    
    /**
     *
     * @var string
     */
    protected $columnName;
    
    /**
     *
     * @var mixed
     */
    protected $value;
    
    /**
     * Constructor
     *
     * @param string $operator
     * @param string $columnName
     * @param string $value
     * @param string $groupName
     */
    public function __construct($operator = null, $columnName = null, $value = null, $groupName = null)
    {
        if ($operator == null) {
            $operator = Fetcher::TYPE_EQ;
        }
        parent::__construct($operator, $groupName);
        $this->columnName = $columnName;
        $this->value = $value;
    }
    
    /**
     * @return string
     */
    public function getColumnName()
    {
        return $this->columnName;
    }
    
    /**
     *
     * @param string $columnName
     *
     * @return \Berthe\Fetcher\Operation\SimpleOperation
     */
    public function setColumnName($columnName)
    {
        $this->columnName = $columnName;
        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     *
     * @param string $value
     *
     * @return \Berthe\Fetcher\Operation\SimpleOperation
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
