<?php

use Berthe\Composition\Scope;

class ScopeTest extends PHPUnit_Framework_TestCase
{
    /** @var Scope */
    protected $scope;

    /** @var \Berthe\Composition\ComposerManager */
    protected $composerManager;

    /** @var \Berthe\Composition\Resource */
    protected $resource;

    protected function setUp()
    {
        $this->composerManager = $this->getMockBuilder('\Berthe\Composition\ComposerManager')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();

        $this->resource = $this->getMockBuilder('\Berthe\Composition\Resource')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testAddDefaultEmbedsToRootScope()
    {
        $this->scope = new Scope($this->composerManager, $this->resource);

        $this->scope->addDefaultEmbeds(array('foo', 'bar'));

        $this->assertEquals(array('foo', 'bar'), $this->composerManager->getRequestedScopes());
    }

    public function testAddDefaultEmbedsToChildScope()
    {
        $this->scope = new Scope($this->composerManager, $this->resource, 'bar');

        $this->scope->setParentScopes(array('', 'foo'));

        $this->scope->addDefaultEmbeds(array('baz'));

        $this->assertContains('foo.bar.baz', $this->composerManager->getRequestedScopes());
    }
}
