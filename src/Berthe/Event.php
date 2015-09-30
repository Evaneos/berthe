<?php

namespace Berthe;

use League\Event\Event as LeagueEvent;

final class Event extends LeagueEvent implements \JsonSerializable
{
    /**
     * @var array
     */
    private $params;

    /**
     * @param string $eventName
     * @param array $params
     */
    public function __construct($eventName, array $params = array())
    {
        parent::__construct($eventName);
        $this->params = $params;
    }

    /**
     * @param $jsonEvent
     * @return Event
     */
    static public function fromJson($jsonEvent)
    {
        $arrayEvent = json_decode($jsonEvent, true);

        $eventParams = isset($arrayEvent['params']) && is_array($arrayEvent['params']) ? $arrayEvent['params'] : array();

        if(!isset($arrayEvent['name']))
        {
            throw new \InvalidArgumentException('Json representation of an event must contain a name');
        }

        return new self($arrayEvent['name'], $eventParams);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if(!array_key_exists($key, $this->params)) {
            throw new \DomainException(sprintf("Key %s doesn't exist in event %s", $key, $this->getName()));
        }

        return $this->params[$key];
    }

    /**
     * @return string
     */
    public function jsonSerialize()
    {
        return json_encode(array(
            'name' => $this->name,
            'params' => $this->params
        ));
    }
}