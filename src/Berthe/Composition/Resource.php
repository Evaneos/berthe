<?php

namespace Berthe\Composition;

class Resource
{
    /**
     * A collection of data
     *
     * @var array|\ArrayIterator
     */
    protected $data;

    /**
     * A callable to process the data attached to this resource
     *
     * @var callable|string
     */
    protected $composer;

    /**
     * @param array|\ArrayIterator $data
     * @param callable|string $composer
     */
    public function __construct($data, $composer)
    {
        $this->data = $data;
        $this->composer = $composer;
    }

    /**
     * Getter for data
     *
     * @return array|\ArrayIterator
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Getter for transformer
     *
     * @return callable|string
     */
    public function getComposer()
    {
        return $this->composer;
    }
}
