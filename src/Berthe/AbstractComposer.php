<?php

namespace Berthe;

abstract class AbstractComposer implements Composer
{

    public function composeSeveral(array $objects = array(), array $embeds = array())
    {
        return $objects;
    }

    public function composeOne(\Berthe\VO $object, array $embeds = array())
    {
        return $object;
    }
}
