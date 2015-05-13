<?php
namespace Berthe\ErrorHandler;

class FunctionalErrorListException extends \LogicException
{
    protected $errors = array();

    public function __construct()
    {
    }

    public function addError(FunctionalErrorException $error)
    {
        $this->errors[] = $error;
        return $this;
    }

    public function addStringError($msg, array $data = array(), $code = 0)
    {
        $error = new FunctionalErrorException($msg, $code, $data);
        $this->addError($error);
        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function flush()
    {
        $this->errors = array();
    }

    public function hasErrors()
    {
        return (count($this->errors) > 0);
    }

    public function throwMe()
    {
        throw $this;
    }
}
