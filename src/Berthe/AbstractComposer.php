<?php

namespace Berthe;

abstract class AbstractComposer implements Composer {

    public function composeSeveral(array $objects = array())
    {
        return $objects;
    }

    public function composeOne(\Berthe\VO $object)
    {
        return $object;
    }
}