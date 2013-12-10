<?php
namespace Evaneos\Berthe\ErrorHandler;

class Errors extends \LogicException {
    protected $errors = array();

    public function __construct() {}

    public function addError(Error $error) {
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