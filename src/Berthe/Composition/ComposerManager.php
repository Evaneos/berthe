<?php

namespace Berthe\Composition;

class ComposerManager {
     
    protected $requestedScopes = array();

    public function __construct(ComposerProvider $composerProvider)
    {
        $this->composerProvider = $composerProvider;   
    }

    public function getComposer($composerName)
    {
        return $this->composerProvider->getComposer($composerName);
    }

    public function getRequestedScopes()
    {
        return $this->requestedScopes;
    }

    public function setRequestedScopes(array $requestedScopes)
    {
        $this->requestedScopes = $this->parseNestedScopes($requestedScopes);
        return $this;
    }

    public function compose($resource, $scopeIdentifier = null, $parentScopeInstance = null)
    {   
        $scopeInstance = new Scope($this, $resource, $scopeIdentifier);

        // Update scope history
        if ($parentScopeInstance !== null) {

            // This will be the new children list of parents (parents parents, plus the parent)
            $scopeArray = $parentScopeInstance->getParentScopes();
            $scopeArray[] = $parentScopeInstance->getCurrentScope();

            $scopeInstance->setParentScopes($scopeArray);
        }

        return $scopeInstance;
    }

    protected function parseNestedScopes(array $scopes)
    {
        $parsed = array();

        foreach ($scopes as $scope) {
            $nested = explode('.', $scope);

            $part = array_shift($nested);
            $parsed[] = $part;

            while (count($nested) > 0) {
                $part .= '.'.array_shift($nested);
                $parsed[] = $part;
            }
        }

        return array_values(array_unique($parsed));
    }

}