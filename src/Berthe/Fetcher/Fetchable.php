<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;

interface Fetchable
{
    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function getByFetcher(Fetcher $fetcher);
}
