<?php

namespace Berthe;

interface Mapper {

    public function composeSeveral($objects);

    public function composeOne($object);

    public function updateOne($object, $data);

}