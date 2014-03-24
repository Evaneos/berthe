<?php

namespace Berthe\Composition;

class Scope {

    protected $composerManager;

    protected $resource;

    protected $parentScopes = array();

    protected $currentScope;

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
        
        // Get embeded composed models
        $embededModels = $composer->getEmbededModels($this, $this->resource->getData());
        
        // Compose resource
        $embededModels = $embededModels ? $embededModels : array();
        
        return $composer->compose($this->resource->getData(), $embededModels);
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