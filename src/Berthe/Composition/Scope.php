<?php

namespace Berthe\Composition;

class Scope {

    /**
     * @var ComposerManager
     */
    protected $composerManager;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var Scope[]
     */
    protected $parentScopes = array();

    /**
     * @var string
     */
    protected $currentScope;

    /**
     * @param ComposerManager $composerManager
     * @param Resource $resource
     * @param string|null $currentScope
     */
    public function __construct(ComposerManager $composerManager, Resource $resource, $currentScope = null)
    {
        $this->composerManager = $composerManager;
        $this->resource = $resource;
        $this->currentScope = $currentScope;
    }

    function getComposedChildScope($scopeIdentifier, $resource)
    {
        return $this->composerManager->compose($resource, $scopeIdentifier, $this);
    }

    public function getComposite()
    {
        $composerName = $this->resource->getComposer();

        $composer = $this->composerManager->getComposer($composerName);

        $data = is_object($this->resource->getData()) ? array($this->resource->getData()) : $this->resource->getData();

        // Get embeded composed models
        $embededModels = $composer->getEmbededModels($this, $data);

        // Compose resource
        $embededModels = $embededModels ? $embededModels : array();

        return $composer->compose($data, $embededModels);
    }

    public function isRequested($checkScopeSegment)
    {
        if ($this->parentScopes) {
            $scopeArray = array_slice($this->parentScopes, 1);
            array_push($scopeArray, $this->currentScope, $checkScopeSegment);
        } else {
            $scopeArray = array($checkScopeSegment);
        }
        $scopeString = implode('.', (array) $scopeArray);


        $checkAgainstArray = $this->composerManager->getRequestedScopes();

        return in_array($scopeString, $checkAgainstArray);
    }

    /**
     * @param string[] $defaultEmbeds
     */
    public function addDefaultEmbeds(array $defaultEmbeds)
    {
        $stringScope = $this->getStringScope();
        if ($stringScope) {
            array_walk($defaultEmbeds, function(&$value) use($stringScope) {
                $value = $stringScope . '.' . $value;
            });
        }

        $this->composerManager->addRequestedScopes($defaultEmbeds);
    }

    /**
     * @return string The full scope string
     */
    protected function getStringScope()
    {
        if ($this->parentScopes) {
            $scopes = array_slice($this->parentScopes, 1);
            $scopes[] = $this->currentScope;
            return implode('.', $scopes);
        }
        return '';
    }

    /**
     * Getter for currentScope
     *
     * @return mixed
     */
    public function getCurrentScope()
    {
        return $this->currentScope;
    }

    /**
     * Setter for parentScopes
     *
     * @param mixed $parentScopes Value to set
     *
     * @return self
     */
    public function setParentScopes($parentScopes)
    {
        $this->parentScopes = $parentScopes;
        return $this;
    }

    /**
     * Getter for parentScopes
     *
     * @return mixed
     */
    public function getParentScopes()
    {
        return $this->parentScopes;
    }

}