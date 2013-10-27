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
    protected $data = null;

    public function __construct($message, $code, $data) {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }
}