<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;
use Berthe\Service;

/**
 * Class FetcherIterator
 *
 * Fetch from a Service a result set according to a fetcher
 * and allow to iterate across these results
 *
 * @package Berthe\Fetcher
 */
class FetcherIterator implements \Iterator
{
    /**
     * @var Service
     */
    protected $service;

    /**
     * @var Fetcher
     */
    protected $fetcher;

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $nbByPage = 10;

    /**
     * @var int
     */
    protected $currentKey;

    /**
     * @var array
     */
    protected $results = array();

    /**
     * @var int
     */
    protected $nbPages;

    /**
     * @param Service $service
     */
    public function setService(Service $service)
    {
        $this->service = $service;
    }

    /**
     * @param Fetcher $fetcher
     */
    public function setFetcher(Fetcher $fetcher)
    {
        $this->fetcher = $fetcher;
        $this->fetcher->setNbByPage($this->nbByPage);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->results[$this->currentKey];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->results);
        $this->currentKey = key($this->results);

        if (null === $this->currentKey && $this->nbPages > $this->page) {
            $this->page++;
            $this->reloadResults();
        }
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->currentKey !== null;
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->page = 1;
        $this->reloadResults();
        $this->nbPages = $this->fetcher->getNbPages();
    }

    private function reloadResults()
    {
        $this->fetcher->setPage($this->page);
        $this->results = $this->service->getByFetcher($this->fetcher)->getResultSet();
        $this->currentKey = key($this->results);
    }
}
