<?php

namespace Berthe\Util;

use Berthe as Berthe;

class MapReduce
{
    /**
     * Return an array with the properties for the given set of Berthe VO
     * @param string $property (can dig into object using dots (exemple :  propObject.subpropObject.prop))
     * @param array $vos
     * @return array key=vo->id, value=vo->property
     */
    public static function extractProperty($property, array $vos = array())
    {
        $propertyChain = explode(".", $property);

        $ret = array();
        foreach ($vos as /* @var $vo Berthe\AbstractVO */ $vo) {
            $voChainable = $vo;
            $copyChain = $propertyChain;
            if (!($voChainable instanceof Berthe\AbstractVO)) {
                trigger_error("Wrong object given for property extraction");
                continue;
            }
            while (($prop = array_shift($copyChain)) !== null) {
                if (property_exists($voChainable, $prop)) {
                    $voChainable = $voChainable->{$prop};
                }
            }

            $ret[$vo->id] = $voChainable;
        }
        return $ret;
    }

     /**
     * Return an array with the properties for the given set of Berthe VO
     * @param string $property
     * @param array $vos
     * @return array ($vo->id, $vo->property)
     */
    public static function extractPropertyInArray($property, array $vos = array())
    {
        $ret = array();
        foreach ($vos as /* @var $vo Berthe\AbstractVO */ $vo) {
            if (!($vo instanceof Berthe\AbstractVO)) {
                trigger_error("Wrong object given for property extraction");
                continue;
            }
            if (property_exists($vo, $property)) {
                $ret[] = array(
                    'id' => $vo->id,
                    $property => $vo->{$property}
                );
            }
        }
        return $ret;
    }
}
