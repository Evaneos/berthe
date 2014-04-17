<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;

abstract class AbstractPGSQLFetchable extends AbstractFetchable
{
    protected $queryBuilder = null;
    protected $db = null;

    abstract protected function getQuery();

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
        $query = $this->getQuery();

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
        $query = $this->getQuery();

        list($filterInReq, $filterToParameter) = $this->queryBuilder->buildFilters($fetcher);
        $sortInReq = $this->queryBuilder->buildSort($fetcher);
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
                ($query) as sub
            WHERE
                ($filterInReq)
        ) as lastsub
    ORDER BY
        id ASC
    ) as lastsub
ORDER BY
    RANDOM() ASC
{$limit}
SQL;
        }
        else {
            $sql = <<<SQL
SELECT
    DISTINCT id
FROM
    (
    SELECT
        sub.*
    FROM
        ($query)  as sub
    WHERE
        {$filterInReq}
    ORDER BY
        {$sortInReq}
    ) as lastsub
{$limit}
SQL;
        }
     die($sql);

        return $this->db->fetchCol($sql, $filterToParameter);
    }
}