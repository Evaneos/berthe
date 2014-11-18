<?php

class ComposerManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Berthe\Composition\ComposerManager
     */
    protected $composerManager;

    protected function setUp()
    {
        $this->composerManager = $this->getMockBuilder('\Berthe\Composition\ComposerManager')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    public function initialRequestedScopesProvider()
    {
        return array ( // requestedScopesInput, requestedScopesOutput
            array(array('foo'),                             array('foo')),
            array(array('foo', 'bar'),                      array('foo', 'bar')),
            array(array('foo', 'foo.bar.baz'),              array('foo', 'foo.bar', 'foo.bar.baz')),
            array(array('foo.bar'),                         array('foo', 'foo.bar')),
            array(array('college', 'foo', 'foo', 'foo'),    array('college', 'foo')),
        );
    }

    public function additionalRequestedScopesProvider()
    {
        return array ( // initialScopes,  additionalScopes,  requestedScopesOutput
            array(array('foo'), array('foo'),           array('foo')),
            array(array('foo'), array('bar'),           array('foo', 'bar')),
            array(array('foo'), array('foo.bar.baz'),   array('foo', 'foo.bar', 'foo.bar.baz')),
            array(array('foo'), array('bar', 'bar'),    array('foo', 'bar')),
            array(array(),      array('foo.bar.baz'),   array('foo', 'foo.bar', 'foo.bar.baz')),
        );
    }

    /**
     * Test that composerManager well parses initial requested scopes
     *
     * @param array $requestedScopesInput   The array of scopes to add to the composerManager
     * @param array $requestedScopesOutput  The array we expect the composerManager contains
     *
     * @dataProvider initialRequestedScopesProvider
     */
    public function testThatInitialRequestedScopesAreWellParsed($requestedScopesInput, $requestedScopesOutput)
    {
        $this->composerManager->setRequestedScopes($requestedScopesInput);

        $this->assertEquals(count($requestedScopesOutput), count($this->composerManager->getRequestedScopes()));
        foreach($requestedScopesOutput as $scope) {
            $this->assertContains($scope, $this->composerManager->getRequestedScopes());
        }
    }

    /**
     * Test that we can add scopes to a composerManager already populated
     *
     * @param array $initialScopes          The array of initial scopes added to the composerManager
     * @param array $additionalScopes       The array of additional scopes we want to add
     * @param array $requestedScopesOutput  The array we expect the composerManager contains
     *
     * @dataProvider additionalRequestedScopesProvider
     */
    public function testThatAdditionalRequestedScopesAreWellMerged($initialScopes, $additionalScopes, $requestedScopesOutput)
    {
        $this->composerManager->setRequestedScopes($initialScopes);
        $this->composerManager->addRequestedScopes($additionalScopes);

        $this->assertEquals(count($requestedScopesOutput), count($this->composerManager->getRequestedScopes()));
        foreach($requestedScopesOutput as $scope) {
            $this->assertContains($scope, $this->composerManager->getRequestedScopes());
        }
    }
}
