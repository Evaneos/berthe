<?php

namespace Berthe\Util;

class BufferedIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    protected $it;

    /** @var  array */
    protected $buffer;

    /** @var  int */
    protected $i;

    /** @var  int */
    protected $chunkSize;

    /**
     * @param \Traversable $x
     * @param int $chunkSize
     */
    public function __construct($x, $chunkSize)
    {
        if ($x instanceof \Iterator) {
            $this->it = $x;
        } else if ($x instanceof \IteratorAggregate) {
            $this->it = $x->getIterator();
        } else if (is_array($x)) {
            $this->it = new \ArrayIterator($x);
        } else {
            $t = gettype($x);
            throw new \InvalidArgumentException(sprintf('%s  object is not iterable', $t));
        }

        if ($chunkSize < 1) {
            throw new \InvalidArgumentException('chunkSize must be greater than 0');
        }

        $this->chunkSize = $chunkSize;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->fetch();
        $this->i++;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->i;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return !empty($this->buffer);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->it->rewind();
        $this->buffer = array();
        $this->i = 0;
        $this->fetch();
    }

    protected function fetch()
    {
        $this->buffer = array();
        for ($i = 0; $i < $this->chunkSize; $i++) {
            if (!$this->it->valid()) return;
            array_push($this->buffer, $this->it->current());
            $this->it->next();
        }
    }
}
