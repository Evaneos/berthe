<?php

/**
 * Class definition for error handler Berthe_ErrorHandler_Errors
 *
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0
 * @filesource Berthe/ErrorHandler/Errors.php
 * @package Berthe
 */
class Berthe_ErrorHandler_Errors extends LogicException {
    protected $errors = array();

    public function __construct() {}

    public function addError(Berthe_ErrorHandler_Error $error) {
        $this->errors[] = $error;
        return $this;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function flush() {
        $this->errors = array();
    }

    public function hasErrors() {
        return (count($this->errors) > 0);
    }

    public function throwMe() {
        throw $this;
    }
}