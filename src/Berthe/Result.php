<?php
/**
 * Result class to be returned by a service.
 *
 * @author tony
 */
class Berthe_Result {
    /**
     * A stack of data
     * 
     * @var array
     */
    protected $_data    = array();
    /**
     * Did the call succeeded ?
     * @var boolean
     */
    protected $_success = false;
    /**
     * The errors that occured
     * @var Exception
     */
    protected $_errors  = array();
    
    /**
     * Constructor... empty for the moment
     */
    public function __construct() {
    }
    
    /**
     * Checks the validity of setted datas
     * @param mixed $data
     * @return boolean true if data is valid
     */
    protected function _checkData($data) {
        return true;
    }
    
    /**
     * Adds data to the result object datas stack
     * @param mixed $value The data to add
     * @param mixed $key   The key where to add the data in the stack
     * @throws InvalidArgumentException $key should be scalar and $value should pass _checkData method
     */
    public function addData($value, $key = null) {
        if($this->_checkData($value)) {
            if($key !== null && is_scalar($key)) {
                $this->_data[$key] = $value;
            } else if($key === null) {
                $this->_data[] = $value;
            } else {
                throw new InvalidArgumentException('$key should be a scalar or null.');
            }
        } else {
            throw new InvalidArgumentException('$value did not pass _checkData method.');
        }
    }
    
    /**
     * Returns the stack of datas
     * @return array
     */
    public function getDatas() {
        return $this->_data;
    }
    
    /**
     * Gets or sets the success
     * @param boolean|null $success [OPTIONAL] If a boolean will set the success of the result object, null by default
     * @return boolean the success
     * @throws InvalidArgumentException $success should be null or boolean
     */
    public function succeded($success = null) {
        if(is_bool($success)) {
            $this->_success = $success;
        } else if($success !== null) {
            throw new InvalidArgumentException('$success should be a boolean or null.');
        }
        return $this->_success;
    }
    
    /**
     * Checks the validity of an exception
     * @param Exception $exception
     * @return boolean
     */
    protected function _checkException($exception) {
        return ($exception instanceof Exception);
    }
    
    /**
     * Adds an excption to the stack
     * @param Exception $exception
     * @throws InvalidArgumentException Exception should be an Exception or extends Exception
     */
    public function addException(Exception $exception) {
        if($this->_checkException($exception)) {
            $this->_data[] = $exception;
        } else {
            throw new InvalidArgumentException('$exception did not pass _checkException method.');
        }
    }
    
    /**
     * Returns the stack of exceptions
     * @return array
     */
    public function getExceptions() {
        return $this->_errors;
    }
}
