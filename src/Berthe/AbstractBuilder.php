<?php
namespace Berthe;

abstract class AbstractBuilder implements Builder {
    
    public function updateFromArray($object, $data = null) {
        return $object;
    }
}