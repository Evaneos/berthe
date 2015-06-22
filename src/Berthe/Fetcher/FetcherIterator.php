<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;
use Berthe\Service;
use Berthe\VO;

/**
 * Class FetcherIterator
 *
 * Fetch from a Fetchable a result set according to a fetcher
 * and allow to iterate across these results
 *
 * @package Berthe\Fetcher
 */
class FetcherIterator implements \Iterator
{
    /**
     * @var Fetchable
     */
    protected $fetchable;

    /**
     * @var Fetcher
     */
    protected $fetcher;

    /**
     * @var int
     */
    protected $lastId;

    /**
     * @var int
     */
    protected $currentKey;

    /**
     * @var array
     */
    protected $results = array();

    /**
     * @var bool
     */
    protected $ended = false;

    /**
     * @param Fetchable $fetchable
     * @param Fetcher $fetcher
     */
    public function __construct(Fetchable $fetchable, Fetcher $fetcher)
    {
        $this->fetchable = $fetchable;
        $this->fetcher = $fetcher;
        $this->fetcher->setPage(1);
        $this->fetcher->sortById(Fetcher::SORT_ASC);
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
        /** @var VO $vo */
        $vo = next($this->results);
        $this->currentKey = key($this->results);

        if ($this->currentKey === null) {
            if ($this->ended !== true) {
                $this->reloadResults();
            }
        } else {
            $this->lastId = $vo->getId();
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
        $this->lastId = null;
        $this->ended = false;
        $this->reloadResults();
    }

    private function reloadResults()
    {
        if ($this->lastId !== null) {
            $this->fetcher->filterByGreaterThanId($this->lastId);
        }
        $this->results = $this->fetchable->getByFetcher($this->fetcher)->getResultSet();
        if (empty($this->results)) {
            $this->ended = true;
            $this->currentKey = null;
        } else {
            if (count($this->results) !== $this->fetcher->getNbByPage()) {
                $this->ended = true;
            }
            $this->currentKey = key($this->results);
            $vo = current($this->results);
            $this->lastId = $vo->getId();
        }
    }
}
