<?php

use Berthe\DAL\FetcherPGSQLQueryBuilder;
use Berthe\Fetcher;

class FakeFetcher extends Fetcher
{
    public function filterByIds(array $ids)
    {
        $this->addFilter('id', Fetcher::TYPE_IN, $ids);
        return $this;
    }

    public function filterByThingyId($id)
    {
        $this->addFilter('thingy_id', Fetcher::TYPE_EQ, $id);
        return $this;
    }
}

class FetcherPGSQLQueryBuilderTest extends PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_builds_query_with_in_filter_and_id_filter()
    {
        $fetcher = (new FakeFetcher())
            ->filterByIds([1, 2, 3])
            ->filterByThingyId(6543);

        list($query, $params) = (new FetcherPGSQLQueryBuilder())->buildFilters($fetcher);

        self::assertEquals(
            '((((id  = ? ) OR (id  = ? ) OR (id  = ? ))) AND (thingy_id  = ? ))',
            $query
        );
        self::assertEquals([1, 2, 3, 6543], $params);
    }

    /** @test */
    public function it_builds_query_with_empty_in_filter_and_id_filter()
    {
        $fetcher = (new FakeFetcher())
            ->filterByIds([])
            ->filterByThingyId(6543);

        list($query, $params) = (new FetcherPGSQLQueryBuilder())->buildFilters($fetcher);

        self::assertEquals('1=2', $query);
        self::assertEmpty($params);
    }

    /** @test */
    public function it_builds_inclusive_query_without_filters()
    {
        list($query, $params) = (new FetcherPGSQLQueryBuilder())
            ->buildFilters(new FakeFetcher());

        self::assertEquals('1=1', $query);
        self::assertEmpty($params);
    }
}
