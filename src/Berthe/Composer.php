<?php

namespace Berthe;

interface Composer {

    public function composeSeveral(array $objects = array(), array $embeds = array());

    public function composeOne(\Berthe\VO $object, array $embeds = array());
}