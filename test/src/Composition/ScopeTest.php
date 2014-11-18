<?php

use Berthe\Composition\Scope;

class ScopeTest extends PHPUnit_Framework_TestCase
{
    /** @var Scope */
    protected $scope;

    protected function setUp()
    {
        $this->scope = $this->getMockBuilder('\Berthe\Composition\Scope')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @param array|null    $parentScopes
     * @param string|null   $currentScope
     * @param string        $expectedStringScope
     *
     * @dataProvider scopeStringCalculatorProvider
     */
    public function testThatScopeStringIsWellCalculated($parentScopes, $currentScope, $expectedStringScope)
    {
        //set value for protected currentScope
        $property = new \ReflectionProperty(get_class($this->scope), 'currentScope');
        $property->setAccessible(true);
        $property->setValue($this->scope, $currentScope);

        $this->scope->setParentScopes($parentScopes);

        //set getStringScope to public
        $method = new \ReflectionMethod(get_class($this->scope), 'getStringScope');
        $method->setAccessible(true);

        //call getStringScope
        $this->assertEquals($expectedStringScope, $method->invoke($this->scope));
    }

    public function scopeStringCalculatorProvider()
    {
        return array(
            array(null,                     null,   ''),
            array(array(''),                'foo',  'foo'),
            array(array('', 'foo'),         'bar',  'foo.bar'), //1st parent is always empty
            array(array('', 'foo',  'bar'), 'baz',  'foo.bar.baz'),
        );
    }
}
