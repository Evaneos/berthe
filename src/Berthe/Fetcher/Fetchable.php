<?php

namespace Berthe\Fetcher;

use Berthe\Exception\NotUniqueResultException;
use Berthe\Fetcher;
use Berthe\VO;

interface Fetchable
{
    /**
     * @param Fetcher $fetcher
     * @return int[]
     */
    public function getIdsByFetcher(Fetcher $fetcher);

    /**
     * @param  Fetcher $fetcher
     * @return int
     */
    public function getCountByFetcher(Fetcher $fetcher);

    /**
     * @param Fetcher $fetcher
     * @return Fetcher
     */
    public function getByFetcher(Fetcher $fetcher);

    /**
     * @param Fetcher $fetcher
     * @return VO|null
     */
    public function getFirstByFetcher(Fetcher $fetcher);

    /**
     * @param Fetcher $fetcher
     * @return VO|null
     * @throws NotUniqueResultException when there is at least 2 results
     */
    public function getUniqueByFetcher(Fetcher $fetcher);
}
