<?php

namespace Berthe\Fetcher;

use Berthe\Exception\NotUniqueResultException;
use Berthe\Fetcher;
use Berthe\Manager;
use Berthe\VO;

abstract class AbstractFetchable implements Fetchable
{
    /** @var Manager */
    protected $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Fetcher $fetcher
     */
    abstract protected function checkFetcherValidity(Fetcher $fetcher);

    /**
     * @inheritdoc
     */
    public function getFirstByFetcher(Fetcher $fetcher)
    {
        $fetcher->setPage(1);
        $fetcher->setNbByPage(1);
        $ids = $this->getIdsByFetcher($fetcher);
        if (empty($ids)) {
            return null;
        }
        return $this->manager->getById(reset($ids));
    }

    /**
     * @inheritdoc
     */
    public function getUniqueByFetcher(Fetcher $fetcher)
    {
        $fetcher->setPage(1);
        $fetcher->setNbByPage(2);
        $ids = $this->getIdsByFetcher($fetcher);
        if (empty($ids)) {
            return null;
        }
        if (count($ids) === 2) {
            throw new NotUniqueResultException();
        }
        return $this->manager->getById(reset($ids));
    }
}
