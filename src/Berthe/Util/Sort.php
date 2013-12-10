<?php

namespace Evaneos\Berthe\Util;

use Evaneos\Berthe\ as Berthe;

class Sort {
    /**
     * @param string $property
     * @param array $vos
     * @return array
     */
    public static function byProperty($property, array $vos = array()) {
        $extract = MapReduce::extractProperty($property, $vos);
        asort($extract);
        $ret = array();
        foreach(array_keys($extract) as $idSibling) {
            $ret[$idSibling] = $vos[$idSibling];
        }
        return $ret;
    }
}