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

    /**
     * @test
     */
    public function it_should_be_JsonSerializable()
    {
        $this->assertInstanceOf('JsonSerializable', new Event('eventName'));
    }

    /**
     * @test
     */
    public function it_should_return_a_json_representation_of_itself()
    {
        $event = new Event('eventName', array('aData' => 'aValue'));

        $this->assertEquals(json_encode(array(
            'name' => 'eventName',
            'params' => array(
                'aData' => 'aValue'
            )
        )),
        $event->jsonSerialize());
    }

    /**
     * @test
     */
    public function it_should_allow_to_be_constructed_from_json()
    {
        $jsonEvent = json_encode(array(
            'name' => 'eventName',
            'params' => array(
                'aData' => 'aValue'
            )
        ));

        $event = Event::fromJson($jsonEvent);

        $this->assertEquals(new Event('eventName', array(
            'aData' => 'aValue'
        )),
        $event);
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function it_should_throw_an_InvalidArgumentException_if_json_version_of_event_doesnt_contain_a_name()
    {
        $jsonEvent = json_encode(array(
            'params' => array(
                'aData' => 'aValue'
            )
        ));

        Event::fromJson($jsonEvent);
    }

    /**
     * @test
     */
    public function it_should_be_possible_to_build_an_event_from_json_without_params()
    {
        $jsonEvent = json_encode(array(
            'name' => 'eventName'
        ));

        $event = Event::fromJson($jsonEvent);

        $this->assertEquals(new Event('eventName'), $event);
    }

}