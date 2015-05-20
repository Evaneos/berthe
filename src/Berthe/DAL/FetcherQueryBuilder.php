<?php

namespace Berthe\DAL;

use Berthe\Fetcher;

interface FetcherQueryBuilder
{
    public function buildSort(Fetcher $fetcher);
    public function buildFilters(Fetcher $fetcher);
    public function buildLimit(Fetcher $fetcher);
}
