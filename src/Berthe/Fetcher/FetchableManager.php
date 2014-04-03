<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;

class FetchableManager
{
    protected $fetchables = array();

    public function addFetchable(Fetchable $fetchable)
    {
        $this->fetchables[] = $fetchable;
    }

    public function fetch(Fetcher $fetcher)
    {
        foreach($this->fetchables as $fetchable) {
            $fetcher = $fetchable->fetch($fetcher);
            if (count($fetcher->getResultSet())) {
                break;
            }
        }

        return $fetcher;
    }
}