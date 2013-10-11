<?php

/**
 * Class definition for error handler Evaneos_Berthe_ErrorHandler
 * 
 * @author anthony@evaneos.Com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/ErrorHandler.php
 * @package Evaneos/Berthe
 * @since Berthe
 */
class Evaneos_Berthe_ErrorHandler extends LogicException {
    const CONST_START_GENERIC       = 1000000;
    const CONST_START_SERVICEAUTH   = 1001000;
    const CONST_START_TEMPMANAGER   = 1003000;
    const CONST_START_TEMPVALIDATOR = 1004000;
    const CONST_START_PAYMENT       = 1005000;
    const CONST_START_ITINERARY     = 1006000;
    const CONST_START_HOME          = 1007000;
    const CONST_START_ARTICLE       = 1008000;
    const CONST_START_AGENCY        = 1009000;
    const CONST_START_USER          = 1010000;
    const CONST_START_AGENT         = 1011000;
    const CONST_START_CRONCTRL      = 1012000;
    
    /**
     * @var Evaneos_Berthe_ErrorHandler_Error[]
     */
    protected $_stack = array();
    /**
     * @var integer
     */
    protected $_index = 0;
    /**
     * @var integer
     */
    protected $_count = 0;
    /**
     * Adds an exception to the stack
     * @param string $message
     * @param integer $code
     * @param mixed $data
     * @param Exception $previous 
     */
    public function add($message, $code, $data = null, Exception $previous = null) {
        if($previous) {
            $_error = new Evaneos_Berthe_ErrorHandler_Error($message, $code, $previous);
        } else {
            $_error = new Evaneos_Berthe_ErrorHandler_Error($message, $code);
        }
        $_error->setData($data);
        $this->append($_error);
    }
    public function addException(Exception $e, $data) {
        if($e->getPrevious() instanceof Exception) {
            $_error = new Evaneos_Berthe_ErrorHandler_Error($e->getMessage(), $e->getCode(), $e->getPrevious());
        } else {
            $_error = new Evaneos_Berthe_ErrorHandler_Error($e->getMessage(), $e->getCode());
        }
        $_error->setData($data);
        $this->append($_error);
    }
    /**
     * Appends an error to the stack (same as Evaneos_Berthe_ErrorHandler::add, but parametter is an already instanciated error
     * @param Evaneos_Berthe_ErrorHandler_Error $error 
     */
    public function append(Evaneos_Berthe_ErrorHandler_Error $error) {
        $this->_stack[] = $error;
        $this->_compute();
    }
    /**
     * Returns the error having the pointer on it
     * @return null 
     */
    public function current() {
        if(isset($this->_stack[$this->_index])) {
            return $this->_stack[$this->_index];
        }
        return false;
    }
    /**
     * Gets current pointed error and Forward the pointer
     * @return Evaneos_Berthe_ErrorHandler_Error 
     */
    public function fetch() {
        $_current = $this->current();
        $this->forward();
        return $_current;
    }
    /**
     * Gets current pointed error and rewind the pointer
     * @return Evaneos_Berthe_ErrorHandler_Error 
     */
    public function fetchBack() {
        $_current = $this->current();
        $this->rewind();
        return $_current;
    }
    /**
     * Forward the pointer and get the next error
     * @return Evaneos_Berthe_ErrorHandler_Error 
     */
    public function next() {
        $this->forward();
        return $this->current();
    }
    /**
     * Forward the pointer and get the previous error
     * @return Evaneos_Berthe_ErrorHandler_Error 
     */
    public function previous() {
        $this->rewind();
        return $this->current();
    }
    /**
     * Increments the pointer 
     */
    public function forward() {
        if($this->_index <= $this->_count) {
            $this->_index++;
        }
    }
    /**
     * Increments the pointer 
     */
    public function fastForward() {
        if($this->_index <= $this->_count) {
            $this->_index = $this->_count - 1;
        }
    }
    /**
     * Decrement the pointer 
     */
    public function rewind() {
        if($this->_index >= 0) {
            $this->_index --;
        }
    }
    /**
     * Place pointer at the end of the stack and return last error
     * @return Evaneos_Berthe_ErrorHandler_Error 
     */
    public function last() {
        $this->fastForward();
        return $this->current();
    }
    /**
     * Place pointer at the start of the stack and return first error
     * @return Evaneos_Berthe_ErrorHandler_Error 
     */
    public function first() {
        $this->reset();
        return $this->current();
    }
    /**
     * Resets the pointer and stack 
     */
    public function reset() {
        $this->_index = 0;
        reset($this->_stack);
    }
    /**
     * Throws the Evaneos_Berthe_ErrorHandler as a LogicException
     */
    public function throwMe() {
        throw $this;
    }
    /**
     * Computes the object 
     */
    protected function _compute() {
        $this->_count = count($this->_stack);
        $this->reset();
    }
    public function hasErrors() {
        return ($this->_count > 0);
    }
}