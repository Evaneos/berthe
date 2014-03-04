<?php

namespace Berthe;

abstract class AbstractComposer implements Composer {

    public function composeSeveral($objects)
    {
        return $objects;
    }

    public function composeOne($object)
    {
        return $object;
    }
}