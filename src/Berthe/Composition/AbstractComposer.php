<?php

namespace Berthe\Composition;

abstract class AbstractComposer
{
    /**
     * Embed if requested
     *
     * @var string[]
     */
    protected $availableEmbeds = array();

    /**
     * Embed without needing it to be requested
     *
     * @var string[]
     */
    protected $defaultEmbeds = array();

    /**
     * A callable to process the data attached to this resource
     *
     * @var ComposerManager
     */
    protected $manager;

    /**
     * Getter for availableEmbeds
     *
     * @return array
     */
    public function getAvailableEmbeds()
    {
        return $this->availableEmbeds;
    }

    /**
     * Getter for defaultEmbeds
     *
     * @return array
     */
    public function getDefaultEmbeds()
    {
        return $this->defaultEmbeds;
    }

    /**
     * Getter for manager
     *
     * @return \Berthe\Composition\ComposerManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * @internal
     * @param Scope $scope
     * @param       $data
     * @return array|bool
     * @throws \Exception
     */
    public function getEmbededModels(Scope $scope, $data)
    {
        $embeddedData = array();
        $embeddedDataCount = 0;
        $defaultEmbeds = array();

        if ($this->defaultEmbeds) {
            $scope->addDefaultEmbeds($this->defaultEmbeds);

            // clean multi level embeds to keep only the 1st child
            $defaultEmbeds = array_map(function ($embed) {
                $embedKeys = explode('.', $embed, 2);
                return $embedKeys[0];
            }, $this->defaultEmbeds);
        }

        $embeds = array_unique(array_merge($defaultEmbeds, $this->availableEmbeds));

        foreach ($embeds as $potentialEmbed) {
            // Check if an available embed is requested
            if (! $scope->isRequested($potentialEmbed)) {
                continue;
            }

            if (! ($resource = $this->callGetMethod($potentialEmbed, $data, $embeddedData))) {
                continue;
            }

            $embeddedData[$potentialEmbed] = $scope->getComposedChildScope($potentialEmbed, $resource)->getComposite();
            ++$embeddedDataCount;
        }

        return $embeddedDataCount === 0 ? false : $embeddedData;
    }

    /**
     * @param string $embed
     * @param array $data
     * @param array $embeddedData
     * @return Resource|false
     * @throws \Exception
     */
    protected function callGetMethod($embed, $data, array $embeddedData)
    {
        // Check if the method name actually exists
        $methodName = 'get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $embed)));

        $resource = call_user_func(array($this, $methodName), $data, $embeddedData);

        if ($resource === null) {
            return false;
        }

        if (! $resource instanceof Resource) {
            throw new \Exception(sprintf(
                'Invalid return value from %s::%s(). Expected %s, received %s.',
                __CLASS__,
                $methodName,
                'Berthe\Composition\ComposerManager\Resource',
                gettype($resource)
            ));
        }

        return $resource;
    }

    /**
     * @param array $objects
     * @param array $embeddedModels
     * @return array
     */
    public function compose(array $objects = array(), array $embeddedModels = array())
    {
        $compositesIndexed = array();
        $composites = array();
        foreach ($objects as $key => $object) {
            if (isset($compositesIndexed[$object->getId()])) {
                $composites[$key] = $compositesIndexed[$object->getId()];
                continue;
            }

            $composite = $this->createComposite($object);
            $compositesIndexed[$object->getId()] = $composite;
            $composites[$key] = $composite;
            $this->composeObject($object, $composite, $embeddedModels);
        }

        return $composites;
    }

    /**
     * Setter for manager
     * @param ComposerManager $manager
     * @return self
     */
    public function setManager(ComposerManager $manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Setter for availableEmbeds
     *
     * @param string[] $availableEmbeds
     * @return self
     */
    public function setAvailableEmbeds(array $availableEmbeds)
    {
        $this->availableEmbeds = $availableEmbeds;
        return $this;
    }

    /**
     * Setter for defaultEmbeds
     *
     * @param string[] $defaultEmbeds
     * @return self
     */
    public function setDefaultEmbeds(array $defaultEmbeds)
    {
        $this->defaultEmbeds = $defaultEmbeds;
        return $this;
    }

    /**
     * Create a new item resource object
     *
     * @param array|\ArrayIterator $data
     * @param callable $transformer
     * @return Resource
     */
    protected function resource($data, $transformer)
    {
        return new Resource($data, $transformer);
    }
}
