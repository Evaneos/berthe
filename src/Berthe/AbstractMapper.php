<?php

namespace Berthe;

abstract class AbstractMapper implements Mapper {

    public function composeSeveral($objects)
    {
        return $objects;
    }

    public function composeOne($object)
    {
        return $object;
    }

    abstract public function updateOne($object, $data);
}