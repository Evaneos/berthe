<?php

namespace Berthe;

interface Composer {

    public function composeSeveral(array $objects = array());

    public function composeOne(\Berthe\VO $object);
}