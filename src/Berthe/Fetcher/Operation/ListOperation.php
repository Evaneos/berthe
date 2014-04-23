<?php
namespace Berthe\Fetcher\Operation;

use Berthe\Fetcher;
use Berthe\Fetcher\FetcherOperation;
class ListOperation extends AbstractOperation
{
    /**
     * 
     * @var array<FetcherOperation>
     */
    protected $operations = array();
    
    /**
     * Constructor
     *
     * @param string $operator
     */
    public function __construct($operator = null, $groupName = null) {
        if ($operator == null) {
            $operator = Fetcher::OPERATOR_AND;
        }
        parent::__construct($operator, $groupName);
        $this->operations= array();
    }
    
    /**
     * 
     * @return array<FetcherOperation>
     */
    public function getOperations() {
        return $this->operations;
    }
    
    /**
     * 
     * @param array $operations
     * 
     * @return \Berthe\Fetcher\Operation\ListOperation
     */
    public function setOperations(array $operations) {
        $this->operations = $operations;
        return $this;
    }
    
    /**
     * 
     * @param FetcherOperation $operation
     * 
     * @return \Berthe\Fetcher\Operation\ListOperation
     */
    public function addOperation(FetcherOperation $operation) {
        $groupName = $operation->getGroupName();
        if ($groupName != null && $groupName != $this->groupName) {
            $this->operations[$groupName] = $operation;
        } else {
            $this->operations[] = $operation;
        }
        return $this;
    }
    
    /**
     *
     * @return array<FetcherOperation>
     */
    public function getOperation($groupName) {
        if (array_key_exists($groupName, $this->operations)) {
            return $this->operations[$groupName];
        }
        return null;
    }
}
