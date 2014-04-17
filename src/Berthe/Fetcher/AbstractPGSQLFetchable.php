<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;

abstract class AbstractPGSQLFetchable extends AbstractFetchable
{
    protected $queryBuilder = null;
    protected $db = null;

    abstract protected function getQuery(Fetcher $fetcher=null);

    public function setQueryBuilder(\Berthe\DAL\FetcherPGSQLQueryBuilder $qb) {
        $this->queryBuilder = $qb;
        return $this;
    }

    /**
     * @param DbReader $db
     * @return AbstractReader
     */
    public function setDb(\Berthe\DAL\DbReader $db) {
        $this->db = $db;
        return $this;
    }

    public function fetch(Fetcher $fetcher)
    {
        $this->checkFetcherValidity($fetcher);

        $count = $this->getCountByFetcher($fetcher);
        $ids = $this->getIdsByFetcher($fetcher);
        $objects = $this->manager->getByIds($ids);

        $fetcher->setTtlCount($count);
        $fetcher->set($objects);
        return $fetcher;
    }

    protected function getCountByFetcher(Fetcher $fetcher)
    {
        $query = $this->getQuery($fetcher);

        list($filterInReq, $filterToParameter) = $this->queryBuilder->buildFilters($fetcher);

        $sql = <<<SQL
SELECT
    count(DISTINCT id)
FROM
    ({$query}) as sub
WHERE
    {$filterInReq}
SQL;

        return $this->db->fetchOne($sql, $filterToParameter);
    }

    protected function getIdsByFetcher(Fetcher $fetcher)
    {
        $query = $this->getQuery($fetcher);

        list($filterInReq, $filterToParameter) = $this->queryBuilder->buildFilters($fetcher);
        $sortInReq = $this->queryBuilder->buildSort($fetcher);
        $limit = $this->queryBuilder->buildLimit($fetcher);

        $isRandom = $fetcher->isRandomSort();

        if ($isRandom) {
            $sql = <<<SQL
SELECT
    DISTINCT id
FROM
    (SELECT
        *
    FROM
        ($query) as sub
    WHERE
        {$filterInReq}
    ORDER BY 2
    {$limit}) randomized
SQL;
        }
        else {
            $sql = <<<SQL
SELECT
    DISTINCT id,
    sub.*
FROM
    ($query)  as sub
WHERE
    {$filterInReq}
ORDER BY
    {$sortInReq}
{$limit}
SQL;
        }

        return $this->db->fetchCol($sql, $filterToParameter);
    }
    
    
    protected function getSelectedColumnsjoins($column, $mainTableAlias, array $selectedColumns = array(), $selectedJoin = array(0, -1)) {
        $columns = $this->getColumns();
        if ($columns != null && array_key_exists($column, $columns)) {
            $selectedColumns[$column] = $columns[$column]['select'];
    
            $join = $columns[$column]['join'];
            if ($join[0]<$selectedJoin[0]) {
                $selectedJoin[0] = $join[0];
            }
            if ($join[1]>$selectedJoin[1]) {
                $selectedJoin[1] = $join[1];
            }
        } else {
            $selectedColumns[$column] = $mainTableAlias.'.'.$column;
        }
    
        return array($selectedColumns, $selectedJoin);
    }
    
    protected function getQueryParameters(Fetcher $fetcher = null, $mainTableAlias='ref')
    {
        $select = '';
        $from = '';
    
        if ($fetcher != null) {
    
            $selectedColumns = array();
            $selectedJoin = array(0, -1);
    
            $filters = $fetcher->getFilters();
            foreach ($filters as $filter) {
                list($selectedColumns, $selectedJoin) = $this->getSelectedColumnsjoins($filter[3], $mainTableAlias, $selectedColumns, $selectedJoin);
            }
    
            $sorts = $fetcher->getSorts();
            foreach ($sorts as $column=>$sortOrder) {
                list($selectedColumns, $selectedJoin) = $this->getSelectedColumnsjoins($column, $mainTableAlias, $selectedColumns, $selectedJoin);
            }
    
            if (count($selectedColumns)>0) {
                $select = ', '.implode(', ', $selectedColumns);
            }
            if ($selectedJoin[1]>0) {
                $joins = $this->getJoins();
                $selectedJoins = array_slice($joins, $selectedJoin[0], $selectedJoin[1]+1);
                $from = implode(' ', $selectedJoins);
            }
        }
    
        return array($select, $from);
    }
    
    protected function getColumns() {
        return array();
    }
    
    protected function getJoins() {
        return array();
    }
    
}