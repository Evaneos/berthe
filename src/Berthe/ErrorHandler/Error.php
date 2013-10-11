<?php

/**
 * Class definition for VO Evaneos_Berthe_ErrorHandler_Error
 * 
 * @author anthony@evaneos.Com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/ErrorHandler/Error.php
 * @package Evaneos/Berthe/ErrorHandler
 * @since Berthe
 */
class Evaneos_Berthe_ErrorHandler_Error extends LogicException {
    // Properties
    /**
     * A bunch of data that may be used to display
     * @var type 
     */
    protected $_data = null;
    /**
     * Getter for datas
     * @return mixed
     */
    public function getData() {
        return $this->_data;
    }
    /**
     * Getter for datas
     * @return mixed
     */
    public function setData($data) {
        return $this->_data = $data;
    }
}