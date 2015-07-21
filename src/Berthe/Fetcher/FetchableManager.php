<?php

namespace Berthe\Fetcher;

use Berthe\Exception\NotFoundException;
use Berthe\Fetcher;

class FetchableManager implements Fetchable
{
    /** @var Fetchable[] */
    protected $fetchables = array();

    /**
     * @param Fetchable $fetchable
     */
    public function addFetchable(Fetchable $fetchable)
    {
        $this->fetchables[] = $fetchable;
    }


    /**
     * @param Fetcher $fetcher
     * @return int[]|null
     */
    public function getIdsByFetcher(Fetcher $fetcher)
    {
        foreach ($this->fetchables as $fetchable) {
            $ids = $fetchable->getIdsByFetcher($fetcher);
            if (!empty($ids)) {
                return $ids;
            }
        }

        return null;
    }
    /**
     * @param Fetcher $fetcher
     * @return int
     */
    public function getCountByFetcher(Fetcher $fetcher)
    {
        foreach ($this->fetchables as $fetchable) {
            $count = $fetchable->getCountByFetcher($fetcher);
            if ($count) {
                return $count;
            }
        }
    }

    /**
     * @param Fetcher $fetcher
     * @return Fetcher
     */
    public function getByFetcher(Fetcher $fetcher)
    {
        foreach ($this->fetchables as $fetchable) {
            $fetcher = $fetchable->getByFetcher($fetcher);
            if ($fetcher->hasResults()) {
                break;
            }
        }

        return $fetcher;
    }

    /**
     * @param Fetcher $fetcher
     * @return mixed
     */
    public function getFirstByFetcher(Fetcher $fetcher)
    {
        foreach ($this->fetchables as $fetchable) {
            $result = $fetchable->getFirstByFetcher($fetcher);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    /**
     * @param Fetcher $fetcher
     * @return mixed
     */
    public function getUniqueByFetcher(Fetcher $fetcher)
    {
        foreach ($this->fetchables as $fetchable) {
            $result = $fetchable->getUniqueByFetcher($fetcher);
            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }
}
