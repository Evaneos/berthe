<?php
namespace Berthe\ErrorHandler;

class FunctionalErrorException extends \LogicException {
    protected $data = array();

    public function __construct($message, $code, array $data = array()) {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }
}