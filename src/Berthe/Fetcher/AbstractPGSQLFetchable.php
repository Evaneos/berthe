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

    public function getByFetcher(Fetcher $fetcher)
    {
        $this->checkFetcherValidity($fetcher);
        $count = $this->getCountByFetcher($fetcher);
        $ids = $this->getIdsByFetcher($fetcher);
        $objects = $this->manager->getByIds($ids);

        $fetcher->setTtlCount($count);
        $fetcher->set($objects);
        return $fetcher;
    }

    public function getCountByFetcher(Fetcher $fetcher)
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
        $sortWrappingQuery = implode(', ', array_merge(
            array("DISTINCT (lastsub.id) AS id"),
            array_keys($fetcher->getSorts())
        ));
        $limit = $this->queryBuilder->buildLimit($fetcher);

        $isRandom = $fetcher->isRandomSort();

        if ($isRandom) {
            $sql = <<<SQL
SELECT
    id
FROM
    (
    SELECT
        DISTINCT ON (id) id
    FROM
        (
            SELECT
                sub.*
            FROM
                ($query) AS sub
            WHERE
                ($filterInReq)
        ) as lastsub
    ORDER BY
        id ASC
    ) AS lastsub
ORDER BY
    RANDOM() ASC
{$limit}
SQL;
        }
        else {
            $sql = <<<SQL
SELECT
    {$sortWrappingQuery}
FROM
    (
    SELECT
        sub.*
    FROM
        ($query)  AS sub
    WHERE
        {$filterInReq}
    ) AS lastsub
ORDER BY
    {$sortInReq}
{$limit}
SQL;
        }
        return $this->db->fetchCol($sql, $filterToParameter);
    }


    /**
     * @param string $column
     * @param string $mainTableAlias
     * @param string[] $selectedColumns
     * @param bool[] $selectedJoins
     * @return array array(string[] $selectedColumns, bool[] $selectedJoins)
     */
    protected function getSelectedColumnsJoins(
        $column,
        $mainTableAlias,
        array $selectedColumns = array(),
        array $selectedJoins = array()
    ) {
        $columns = $this->getColumns();
        if ($columns != null && array_key_exists($column, $columns)) {
            $selectedColumns[$column] = $columns[$column]['select'];

            if (isset($columns[$column]['join'])) {
                $join = $columns[$column]['join'];
                foreach(range($join[0], $join[1]) as $joinValue) {
                    $selectedJoins[$joinValue] = true;
                }
            } else {
                $joins = $columns[$column]['joins'];
                foreach($joins as $join) {
                    $selectedJoins[$join] = true;
                }
            }
        } else {
            $selectedColumns[$column] = $mainTableAlias.'.'.$column;
        }

        return array($selectedColumns, $selectedJoins);
    }

    /**
     * @param Fetcher $fetcher
     * @param string $mainTableAlias
     * @return string[]
     */
    protected function getQueryParameters(Fetcher $fetcher = null, $mainTableAlias='ref')
    {
        $select = '';
        $from = '';

        if ($fetcher != null) {

            $selectedColumns = array();
            $selectedJoins = array();

            $columns = $fetcher->getFilterColumns();
            foreach ($columns as $column) {
                list($selectedColumns, $selectedJoins) = $this->getSelectedColumnsJoins($column, $mainTableAlias, $selectedColumns, $selectedJoins);
            }

            $sorts = $fetcher->getSorts();
            foreach ($sorts as $column=>$sortOrder) {
                list($selectedColumns, $selectedJoins) = $this->getSelectedColumnsJoins($column, $mainTableAlias, $selectedColumns, $selectedJoins);
            }

            $toUse = array();
            foreach($selectedColumns as $key => $value) {
                if ($mainTableAlias.'.id' !== $value) {
                    $toUse[$key] = $value;
                }
            }

            if (count($toUse)>0) {
                $select = ', '.implode(', ', $toUse);
            }

            if (!empty($selectedJoins)) {
                $joins = $this->getJoins();
                $selectedJoinsSql = array_intersect_key($joins, $selectedJoins);
                $from = implode(' ', $selectedJoinsSql);
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
