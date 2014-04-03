<?php

namespace Berthe\Composition;

abstract class AbstractComposer
{
    /**
     * Embed if requested
     *
     * @var array
     */
    protected $availableEmbeds;

    /**
     * Embed without needing it to be requested
     *
     * @var array
     */
    protected $defaultEmbeds;
    
    /**
     * A callable to process the data attached to this resource
     *
     * @var Berthe\Composition\ComposerManager
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
     * @return Berthe\Composition\ComposerManager
     */
    public function getManager()
    {
        return $this->manager;
    }

    public function getEmbededModels(Scope $scope, $data)
    {
        $embededData = array();
        $embededDataCount = 0;

        if (is_array($this->defaultEmbeds)) {

            foreach ($this->defaultEmbeds as $potentialEmbed) {

                if (! ($resource = $this->callGetMethod($potentialEmbed, $data))) {
                    continue;
                }

                $embededData[$potentialEmbed] = $scope->getComposedChildScope($potentialEmbed, $resource)->getComposite();
                ++$embededDataCount;
            }
        }

        if (is_array($this->availableEmbeds)) {

            foreach ($this->availableEmbeds as $potentialEmbed) {
                // Check if an available embed is requested
                if (! $scope->isRequested($potentialEmbed)) {
                    continue;
                }

                if (! ($resource = $this->callGetMethod($potentialEmbed, $data))) {
                    continue;
                }

                $embededData[$potentialEmbed] = $scope->getComposedChildScope($potentialEmbed, $resource)->getComposite();
                ++$embededDataCount;
            }
        }

        return $embededDataCount === 0 ? false : $embededData;
    }

    protected function callGetMethod($embed, $data)
    {
        // Check if the method name actually exists
        $methodName = 'get'.str_replace(' ', '', ucwords(str_replace('_', ' ', $embed)));

        $resource = call_user_func(array($this, $methodName), $data);

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
     * Setter for manager
     *
     * @return self
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Setter for availableEmbeds
     *
     * @return self
     */
    public function setAvailableEmbeds($availableEmbeds)
    {
        $this->availableEmbeds = $availableEmbeds;
        return $this;
    }

    /**
     * Setter for defaultEmbeds
     *
     * @return self
     */
    public function setDefaultEmbeds($defaultEmbeds)
    {
        $this->defaultEmbeds = $defaultEmbeds;
        return $this;
    }

    /**
     * Create a new item resource object
     *
     * @return Berthe\Composition\Resource
     */
    protected function resource($data, $transformer)
    {
        return new Resource($data, $transformer);
    }
}
