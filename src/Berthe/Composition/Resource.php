<?php

namespace Berthe\Composition;

class Resource
{
    /**
     * A collection of data
     *
     * @var array|ArrayIterator
     */
    protected $data;

    /**
     * A callable to process the data attached to this resource
     *
     * @var callable|string
     */
    protected $composer;

    /**
     * A list of embeds
     *
     * @var array|ArrayIterator
     */
    protected $forcedEmbeds;

    /**
     * @param array|ArrayIterator $data
     * @param callable|string $composer
     * @param  array|ArrayIterator $forcedEmbeds
     */
    public function __construct($data, $composer, $forcedEmbeds = array())
    {
        $this->data = $data;
        $this->composer = $composer;
        $this->forcedEmbeds = $forcedEmbeds;
    }

    /**
     * Getter for data
     *
     * @return array|ArrayIterator
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

    /**
     * Getter for forcedEmbeds
     *
     * @return array|ArrayIterator
     */
    public function getForcedEmbeds()
    {
        return $this->forcedEmbeds;
    }
}
