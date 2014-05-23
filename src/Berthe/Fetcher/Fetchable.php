<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher as Fetcher;

interface Fetchable
{
	/**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    function getByFetcher(Fetcher $fetcher);
}