<?php

/**
 * Class definition for VO Berthe_ErrorHandler_Error
 *
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0
 * @filesource Berthe/ErrorHandler/Error.php
 * @package Berthe/ErrorHandler
 */
class Berthe_ErrorHandler_Error extends LogicException {
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