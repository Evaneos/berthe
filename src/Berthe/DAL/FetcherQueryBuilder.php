<?php

namespace Berthe\DAL;
use Berthe\Fetcher;


interface FetcherQueryBuilder {
    function buildSort(Fetcher $fetcher);
    function buildFilters(Fetcher $fetcher);
    function buildLimit(Fetcher $fetcher);
}