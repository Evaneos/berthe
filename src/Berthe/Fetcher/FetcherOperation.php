<?php
namespace Berthe\Fetcher;

interface FetcherOperation
{
    
    /**
     * @param mixed $operator
     * 
     * @return \Berthe\Fetcher\FetcherOperation;
     */
    public function setOperator($operator);
    
    /**
     * @return mixed the operator
     */
    public function getOperator();
    
    /**
     * @param string $groupName
     * 
     * @return \Berthe\Fetcher\FetcherOperation;
     */
    public function setGroupName($groupName);
    
    /**
     * @return string the groupName
     */
    public function getGroupName();
}
