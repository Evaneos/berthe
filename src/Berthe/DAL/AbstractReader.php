<?php

namespace Berthe\DAL;


abstract class AbstractReader {
    /**
     * Class name of the VO for current package
     */
    const VO_CLASS = '\Berthe\AbstractVO';
    /**
     * @var DbReader
     */
    protected $db = null;

    /**
     * @param DbReader $db
     * @return AbstractReader
     */
    public function setDb(DbReader $db) {
        $this->db = $db;
        return $this;
    }

    /**
     * Returns the Class name of the VO for current package
     * @return string
     */
    public function getVOClass() {
        return static::VO_CLASS;
    }

    /**
     * Implements an bunch of \Berthe\AbstractVO from datas
     * @param array $datas
     * @return \Berthe\AbstractVO
     */
    protected function implementVOs(array $datas = array()) {
        $_ret = array();

        $_class = $this->getVOClass();

        if($_class == self::VO_CLASS) {
            throw new \RuntimeException(__CLASS__ . '::VO_CLASS constant is not defined');
        }

        foreach($datas as &$row) {
            $_ret[$row['id']] = new $_class($row);
        }

        return $_ret;
    }

    /**
     * Returns the Query String to get data for the VOs
     */
    abstract protected function getSelectQuery();

    /**
     * Returns a query from getSelectQuery method by appending a where statement on ids
     * @param array $ids
     * @return string
     */
    protected function getSelectQueryByIds(array $ids = array()) {
        $implode = implode(', ', $ids);

        return <<<EOQ
{$this->getSelectQuery()}
WHERE
    {$this->getTableName()}.id in ($implode)

EOQ;

    }

    /**
     * Gets a bunch of \Berthe\AbstractVO from database from their ids
     * @param array $ids
     * @return \Berthe\AbstractVO
     */
    public function selectByIds(array $ids = array ()) {
        if (count($ids) === 0) {
            return array();
        }

        $ids = array_map('intval', $ids);

        $sql = $this->getSelectQueryByIds($ids);

        $resultSet = $this->db->fetchAll($sql);

        return $this->implementVOs($resultSet);
    }

    /**
     * Extract the list of columns from Select Query
     * @return string[] The list of columns
     */
    protected function _extractColumnsFromSelectQuery() {
        $_query = $this->getSelectQuery();
        $_matches = array();

        $_pattern = '#.*(SELECT)(.*)(FROM|LEFT|JOIN)#ims';
        preg_match($_pattern, $_query, $_matches);

        $_columnsList = explode(',', $_matches[2]);

        array_walk($_columnsList, function(&$value, $index) {
            $value = trim($value);
        });

        return $_columnsList;
    }

    /**
     * Returns the index of selected column in the select query
     * @param string $col
     * @return integer
     */
    protected function _getColumnIndexInSelectQuery($col) {
        $_columns = $this->_extractColumnsFromSelectQuery();

        $_columnIndex = false;
        foreach ($_columns as $index => $column) {
            if($col == $column) {
                $_columnIndex = $index;
                break;
            }
        }
        return $_columnIndex;
    }

    /**
     * Returns the values for $col column in the select query for $ids ids
     * @param array $ids
     * @param string $columnName
     * @return mixed[]
     * @throws InvalidArgumentException
     */
    public function selectColByIdsPreserveIds(array $ids = array(), $columnName = 'id') {
        if (count($ids) === 0) {
            return array();
        }

        $_query = $this->getSelectQueryByIds($ids);
        $_columnIndexId = $this->_getColumnIndexInSelectQuery('id');
        $_columnIndex = $this->_getColumnIndexInSelectQuery($columnName);

        if($_columnIndex === false || $_columnIndexId === false) {
            throw new \InvalidArgumentException(get_called_class() . '::' . __METHOD__ . '() Invalid column name given as second parametter. "' . $columnName . '" given');
        }

        $_stmt1 = $this->db->getAdapter()->query($_query);
        $columnIndexId = $_stmt1->fetchAll(Zend_Db::FETCH_COLUMN, $_columnIndexId);

        $_stmt2 = $this->db->getAdapter()->query($_query);
        $columnIndex = $_stmt2->fetchAll(Zend_Db::FETCH_COLUMN, $_columnIndex);
        $res = array_combine($columnIndexId, $columnIndex);
        if ($res !== false) {
            return $res;
        }
        else {
            throw new \RuntimeException("Not same count between ID and selected COLUMN");
        }
    }

    /**
     * Returns the values for $col column in the select query for $ids ids
     * @param array $ids
     * @param string $columnName
     * @return mixed[]
     * @throws InvalidArgumentException
     */
    public function selectColByIds(array $ids = array(), $columnName = 'id') {
        if (count($ids) === 0) {
            return array();
        }

        $_query = $this->getSelectQueryByIds($ids);
        $_columnIndex = $this->_getColumnIndexInSelectQuery($columnName);

        if($_columnIndex === false) {
            throw new \InvalidArgumentException(get_called_class() . '::' . __METHOD__ . '() Invalid column name given as second parametter. "' . $columnName . '" given');
        }

        $_stmt2 = $this->db->getAdapter()->query($_query);
        return $_stmt2->fetchAll(\Zend_Db::FETCH_COLUMN, $_columnIndex);
    }

    /**
     * Return the table name of the table.
     * @return string
     */
    abstract protected function getTableName();

    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function selectCountByPaginator(\Berthe\Fetcher $paginator) {
        list($filterInReq, $filterToParameter) = $paginator->getFiltersForQuery();

        $sql = <<<EOL
SELECT
    count(id)
FROM
    {$this->getTableName()}
WHERE
    {$filterInReq}
EOL;
        return $this->db->fetchOne($sql, $filterToParameter);
    }

    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function selectByPaginator(\Berthe\Fetcher $paginator) {

        list($sql, $filterToParameter) = $this->getSqlByPaginator($paginator);

        $resultSet = $this->db->fetchCol($sql, $filterToParameter);

        return $resultSet;
    }

    /**
     * @param Fetcher $paginator
     * @return array(string, array) the sql and the array of the parameters
     */
    public function getSqlByPaginator(\Berthe\Fetcher $paginator) {
        list($filterInReq, $filterToParameter) = $paginator->getFiltersForQuery();
        $sortInReq = $paginator->getSortForQuery();
        $isRandom = $paginator->isRandomSort();

        $limit = $paginator->getLimit();
        if ($isRandom) {
            $sql = <<<EOL
SELECT
    id
FROM
    (SELECT
        id,
        RANDOM()
    FROM
        {$this->getTableName()}
    WHERE
        {$filterInReq}
    ORDER BY 2
    {$limit}) randomized
EOL;
        }
        else {
            $sql = <<<EOL
SELECT
    id
FROM
    {$this->getTableName()}
WHERE
    {$filterInReq}
ORDER BY
    {$sortInReq}
{$limit}
EOL;
        }
        return array($sql, $filterToParameter);

    }



}