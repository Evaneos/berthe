<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;

class FetchableManager implements Fetchable
{
    protected $fetchables = array();

    public function addFetchable(Fetchable $fetchable)
    {
        $this->fetchables[] = $fetchable;
    }

    public function getCountByFetcher(Fetcher $fetcher)
    {
        foreach($this->fetchables as $fetchable) {
            $count = $fetchable->getCountByFetcher($fetcher);
            if ($count) {
                return $count;
            }
        }
    }

    public function getByFetcher(Fetcher $fetcher)
    {
        foreach($this->fetchables as $fetchable) {
            $fetcher = $fetchable->getByFetcher($fetcher);
            if (count($fetcher->getResultSet())) {
                break;
            }
        }

        return $fetcher;
    }
}
