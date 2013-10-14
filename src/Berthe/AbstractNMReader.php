<?php

/**
 * AbstractNMReader for N <-> M tables
 *
 * @author ghislain@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Berthe/AbstractNMReader.php
 * @package Berthe
 * @see Berthe/AbstractReader.php
 */
abstract class Berthe_AbstractNMReader extends Berthe_AbstractReader {
    /**
     *
     * @var Berthe_DbReader
     */
    protected $_db = null;

    public function __construct() {
        $this->_db = Zend_Registry::get('dbReader');
    }
    
    /**
     * Return the N name of the NM table.
     * @return string 
     */
    abstract protected function getN();
    
    /**
     * Return the M name of the NM table.
     * @return string 
     */
    abstract protected function getM();
    
    /**
     * Return the additional columns for the NM table.
     * @return array
     */
    protected function getArrayAdditionalColumns() {
        return array();
    }
    
    /**
     * Return the columns as a string ready to be inserted into a query ( "id, colN, colM, add1, add2, add3" ) 
     * @return string ready to be inserted into a query ( "id, colN, colM, add1, add2, add3" ) 
     */
    private function getColumnsToString() {
        return implode(', ', array_merge(array('id', $this->getN(), $this->getM()), $this->getArrayAdditionalColumns()));
    }
    
    /**
     * Returns the Class name of the VO for current package
     * @return string
     */
    public function getVOClass() {
        return self::VO_CLASS;
    }
    
    
    /**
     * Returns the Query String to get data for the VOs
     */
    protected function getSelectQuery() {
        return <<<EOQ
EOQ;
    }
    
    /**
     * Gets a bunch of Berthe_AbstractVO from database from their ids
     * @param type $ids
     * @return type 
     */
    public function selectByIds(array $ids = array()){

	$_ret = array();
        if (count($ids) === 0) return $_ret;

        $implode = implode(', ', $ids);
	$cname = str_replace("DAOReader", "VO", get_class($this));
	$sql = "
	SELECT " . $this->getColumnsToString() . "
	FROM " . $this->getTableName() . "
	WHERE id in (" . $implode . ")";

	$resultSet = $this->_db->fetchAll($sql);

	foreach ($resultSet as $row) {
	    $_ret[$row['id']] = new $cname($row);
	}

	return $_ret;
    }
    
    /**
     * Gets a bunch of Berthe_AbstractVO from database from their N ids
     * @param type $ids
     * @return array 
     */
    protected function selectByNsIds(array $ids = array()){

	$_ret = array();
        if (count($ids) === 0) return $_ret;

        foreach($ids as $id) {
            $_ret[$id] = array();
        }
        
	$implode = implode(', ', $ids);
	$cname = str_replace("DAOReader", "VO", get_class($this));

	$N = $this->getN();

	$sql = " 
	SELECT " . $this->getColumnsToString() . "
	FROM " . $this->getTableName() . "
	WHERE " . $N . " in (" . $implode . ")";

	$resultSet = $this->_db->fetchAll($sql);

	foreach ($resultSet as $row) {

	    if ( !isset($_ret[$row[$N]])){
		$_ret[$row[$N]] = array();
	    }

	    $_ret[$row[$N]][] = new $cname($row);
	}
    
	return $_ret;
    }	     
    
    
    /**
     * Gets a bunch of Berthe_AbstractVO from database from their M ids
     * @param type $ids
     * @return array 
     */
    protected function selectByMsIds(array $ids = array()){ 

	$_ret = array();
        if (count($ids) === 0) return $_ret;

        
	$implode = implode(', ', $ids);
	$cname = str_replace("DAOReader", "VO",  get_class($this));

	$M = $this->getM();

	$sql = " 
	SELECT " . $this->getColumnsToString() . "
	FROM " . $this->getTableName() . "
	WHERE " . $M . " in (" . $implode . ")";

	$resultSet = $this->_db->fetchAll($sql);

	foreach ($resultSet as $row) {

	    if ( !isset($_ret[$row[$M]])){
		$_ret[$row[$M]] = array();
	    }

	    $_ret[$row[$M]][] = new $cname($row);
	}

	return $_ret;
    }	     
    
    /**
     * Gets a bunch of M ids from database from their N ids
     * @param type $ids
     * @return array 
     */
    protected function selectMsByNsIds(array $ids = array()){
        if (count($ids) === 0) return $_ret;

	$implode = implode(', ', $ids);

	$sql = " 
	SELECT " . $this->getM() . " 
	FROM " . $this->getTableName() . "
	WHERE " . $this->getN() . " in (" . $implode . ")";

	$_ret = $this->_db->fetchCol($sql);

	return $_ret; 
    }	 	     
    
    /**
     * Gets a bunch of M ids from database from their N ids
     * @param type $ids
     * @return array 
     */
    protected function selectNsByMsIds(array $ids = array()){
        if (count($ids) === 0) return $_ret;
	$implode = implode(', ', $ids);

	$sql = " 
	SELECT " . $this->getN() . " 
	FROM " . $this->getTableName() . "
	WHERE " . $this->getM() . " in (" . $implode . ")";

	$_ret = $this->_db->fetchCol($sql);

	return $_ret; 
    }	    
    
    /**
     * Gets one row from their M and N id
     * @param type $ids
     * @return Berthe_AbstractNMVO 
     */
    protected function selectByNAndMId($n_id, $m_id){

	$cname = str_replace("DAOReader", "VO",  get_class($this));

	$sql = " 
	SELECT " . $this->getColumnsToString() . "
	FROM " . $this->getTableName() . "
	WHERE " . $this->getN() . " = " . $n_id . " AND " . $this->getM() . " = " . $m_id;

	$_ret = $this->_db->fetchRow($sql);

        if($_ret) {
            return new $cname($_ret); 
        }
        
        return null;
    }
    
    /**
     * @param Fetcher $paginator
     * @return int 
     */
    public function selectCountByPaginator(Fetcher $paginator) {
        list($filterInReq, $filterToParameter) = $paginator->getFiltersForQuery();
        
        $tableName = $this->getTableName();
        
        $sql = <<<EOL
SELECT 
    count(id)
FROM 
    {$tableName}
WHERE
    {$filterInReq}
EOL;
    
        return $this->_db->fetchOne($sql, $filterToParameter);
    }   
    
    /**
     * @param Fetcher $paginator
     * @return array 
     */
    public function selectIdsByPaginator(Fetcher $paginator) {
        list($filterInReq, $filterToParameter) = $paginator->getFiltersForQuery();
        
        $tableName = $this->getTableName();
        $sortInReq = $paginator->getSortForQuery();
        $limit = $paginator->getLimit();
        
        $sql = <<<EOL
SELECT 
    id
FROM 
    {$tableName}
WHERE
    {$filterInReq}
ORDER BY
    {$sortInReq}
{$limit}
EOL;

        return $this->_db->fetchCol($sql, $filterToParameter);
    }
}