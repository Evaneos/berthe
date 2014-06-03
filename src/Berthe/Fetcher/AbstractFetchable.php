<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;

abstract class AbstractFetchable implements Fetchable
{
    protected $manager = null;

    public function __construct(\Berthe\Manager $manager)
    {
        $this->manager = $manager;
    }

    abstract protected function checkFetcherValidity(Fetcher $fetcher);
}
