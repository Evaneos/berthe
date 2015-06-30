<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher;
use Berthe\Service;
use Berthe\VO;
use Berthe\Fetcher\Operation\SimpleOperation;

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
    /** @var Fetchable */
    protected $fetchable;

    /** @var Fetcher */
    protected $fetcher;

    /** @var int */
    protected $lastId;

    /** @var int */
    protected $currentKey;

    /** @var array */
    protected $results = array();

    /** @var bool */
    protected $ended = false;

    /** @var SimpleOperation */
    private $filterIdOperation;

    /**
     * @param Fetchable $fetchable
     * @param Fetcher   $fetcher
     * @throws \Exception
     */
    public function __construct(Fetchable $fetchable, Fetcher $fetcher)
    {
        if ($fetcher->getNbByPage() <= 0) {
            throw new \Exception('Fetcher should have a limit of results per page');
        }

        if ($fetcher->hasSort()) {
            throw new \Exception('Sorted fetcher is not supported');
        }

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
            if ($this->filterIdOperation === null) {
                $this->filterIdOperation = $this->fetcher->filterByGreaterThanId($this->lastId);
            } else {
                $this->filterIdOperation->setValue($this->lastId);
            }
        }

        $this->results = $this->fetchable->getByFetcher($this->fetcher)->getResultSet();

        if (!$this->fetcher->hasResults()) {
            $this->ended = true;
            $this->currentKey = null;
        } else {
            if ($this->fetcher->count() !== $this->fetcher->getNbByPage()) {
                $this->ended = true;
            }

            $this->currentKey = key($this->results);
            /** @var VO $vo */
            $vo = current($this->results);
            if ($vo !== false) {
                $this->lastId = $vo->getId();
            }
        }
    }
}
