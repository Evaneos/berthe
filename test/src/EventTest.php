<?php

use Berthe\Event;

final class EventTest extends PHPUnit_Framework_TestCase
{
    public function testGetNameReturnsEventName()
    {
        $event = new Event('eventName');
        $this->assertEquals('eventName', $event->getName());
    }

    /**
     * @expectedException DomainException
     */
    public function testGetThrowsAnExceptionIfParamsNotSet()
    {
        $event = new Event('eventName', array(
            'aKey' => 'something'
        ));

        $event->get('anUnexistantKey');
    }

    public function testGetReturnsParamIfItWasSet()
    {
        $event = new Event('eventName', array(
            'aKey' => 'something'
        ));

        $this->assertEquals('something', $event->get('aKey'));
    }
}