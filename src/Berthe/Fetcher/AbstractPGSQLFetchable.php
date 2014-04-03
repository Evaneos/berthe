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
    DISTINCT id
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
}