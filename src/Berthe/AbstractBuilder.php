<?php
namespace Berthe;

abstract class AbstractBuilder implements Builder {
    
    public function updateFromArray($object, array $data = array()) {
        return $object;
    }
}