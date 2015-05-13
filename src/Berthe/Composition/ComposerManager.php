<?php

namespace Berthe\Composition;

class ComposerManager
{
    /**
     * @var string[]
     */
    protected $requestedScopes = array();

    /**
     * @param ComposerProvider $composerProvider
     */
    public function __construct(ComposerProvider $composerProvider)
    {
        $this->composerProvider = $composerProvider;
    }

    public function getComposer($composerName)
    {
        return $this->composerProvider->getComposer($composerName);
    }

    /**
     * @return string[]
     */
    public function getRequestedScopes()
    {
        return $this->requestedScopes;
    }

    /**
     * @param string[] $requestedScopes
     *
     * @return self
     */
    public function setRequestedScopes(array $requestedScopes)
    {
        $this->requestedScopes = self::parseNestedScopes($requestedScopes);
        return $this;
    }

    /**
     * @param string[] $requestedScopes
     *
     * @return self
     */
    public function addRequestedScopes(array $requestedScopes)
    {
        $requestedScopes = self::parseNestedScopes($requestedScopes);
        $this->requestedScopes = array_unique(array_merge($this->requestedScopes, $requestedScopes));

        return $this;
    }

    /**
     * Compose a Resource
     *
     * @param Resource $resource
     * @param string|null $scopeIdentifier
     * @param Scope|null $parentScopeInstance
     *
     * @return Scope
     */
    public function compose(Resource $resource, $scopeIdentifier = null, Scope $parentScopeInstance = null)
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

    /**
     * @param string[] $scopes
     * @return string[]
     */
    private static function parseNestedScopes(array $scopes)
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
