<?php
class Berthe_Util_Sort {
    /**
     * @param string $property
     * @param array $vos
     * @return array
     */
    public static function byProperty($property, array $vos = array()) {
        $extract = Berthe_Util_MapReduce::extractProperty($property, $vos);
        asort($extract);
        $ret = array();
        foreach(array_keys($extract) as $idSibling) {
            $ret[$idSibling] = $vos[$idSibling];
        }
        return $ret;
    }
}