<?php

namespace Berthe;

use League\Event\Event as LeagueEvent;

final class Event extends LeagueEvent
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
    public static function fromJson($jsonEvent)
    {
        $arrayEvent = json_decode($jsonEvent, true);

        $eventParams = isset($arrayEvent['params']) && is_array($arrayEvent['params'])?$arrayEvent['params']:array();

        if (!isset($arrayEvent['name'])) {
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
        if (!array_key_exists($key, $this->params)) {
            throw new \DomainException(sprintf("Key %s doesn't exist in event %s", $key, $this->getName()));
        }

        return $this->params[$key];
    }
    
    /**
     * @return array:
     */
    public function getParameters()
    {
        return $this->params;
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

    /**
     * They're equal if this is the same type of event and the same id
     * Allows to know if an event concerns a same type of action on the same object.
     * Very useful to delete doubles.
     *
     * @param  Event $event
     * @return boolean
     */
    public function isEqual(Event $event)
    {
        try {
            return $this->getName() === $event->getName()
            && $this->get('id') === $event->get('id')
            && self::getSortedKeys($this->getParameters()) === self::getSortedKeys($event->getParameters());
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Returns the parameters keys sorted by alphabetical order
     *
     * @param array $params
     * @return array
     */
    private static function getSortedKeys(array $params)
    {
        $keys = array_keys($params);
        sort($keys);
        return $keys;
    }
}
