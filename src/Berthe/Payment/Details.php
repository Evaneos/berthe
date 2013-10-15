<?php

abstract class Berthe_Payment_Details {
    const ERROR_NONE               = 0;
    const ERROR_PROVIDER           = 1;
    const ERROR_EVANEOS            = 2;
    const ERROR_CUSTOMERAMOUNT     = 3;
    const ERROR_INVALIDTRANSACTION = 4;
    const ERROR_MANIPULATION       = 5;
    
    protected $_success       = false;
    protected $_error         = 0;
    protected $_errorHR       = '';
    protected $_howTo         = '';
    protected $_transactionId = 0;
    protected $_providerTransactionId = '';
    protected $_status        = 0;
    protected $_mode          = null;
    
    public function __construct(array $_post = array()) {
        if(!empty($_post)) {
            $this->setDetails($_post);
        }
    }
    
    public function getTransactionId() {
        return $this->_transactionId;
    }
    
    public function getProviderTransactionId() {
        return $this->_providerTransactionId;
    }
    
    public function succeeded() {
        return $this->_success;
    }
    
    public function getErrorCode() {
        return $this->_error;
    }
    
    public function getErrorHumanReadable() {
        return $this->_errorHR;
    }
    
    public function getHowToFix() {
        return $this->_howTo;
    }
    
    public function getStatus() {
        return $this->_status;
    }
    
    public function getMode() {
        return $this->_mode;
    }

    abstract public function setDetails($details);
}