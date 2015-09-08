<?php

namespace Berthe;

use League\Event\Event as LeagueEvent;

final class Event extends LeagueEvent
{
    private $params;

    public function __construct($eventName, array $params = array())
    {
        parent::__construct($eventName);
        $this->params = $params;
    }

    public function get($key)
    {
        if(!array_key_exists($key, $this->params)) {
            throw new \DomainException(sprintf("Key %s doesn't exist in event %s", $key, $this->getName()));
        }

        return $this->params[$key];
    }
}