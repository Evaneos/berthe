<?php

namespace Berthe;

interface Composer {

    public function composeSeveral($objects);

    public function composeOne($object);
}