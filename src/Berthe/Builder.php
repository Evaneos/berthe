<?php
namespace Berthe;

interface Builder {

    /**
     * Updates a VO according to the data passed in parameter
     * 
     * @param VO     $object
     * @param array  $data
     * 
     * @return VO
     */
    public function updateFromArray($object, array $data = array());

}