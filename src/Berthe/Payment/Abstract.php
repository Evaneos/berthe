<?php

abstract class Evaneos_Berthe_Payment_Abstract {
    
    const MODE = 'payline';
    const CONST_ERROR_PLAYINE = 1005100;
    const CONST_ERROR_SLIMPAY = 1005200;

    /**
     *
     * @var Berthe_Modules_Transaction_VO
     */
    protected $_transaction = null;
    /**
     *
     * @var Berthe_FactoryManager 
     */
    protected $_factoryManager = null;

    /**
     *
     * @var string
     */
    protected $_email = '';
    /**
     * @var Evaneos_Berthe_ErrorHandler
     */
    protected $_errorHandler = null;

    public function __construct() {
        $this->_errorHandler = new Evaneos_Berthe_ErrorHandler('Payment Error handler', 0, null);
        $_context = Berthe_Context::getInstance();
        $this->_factoryManager = Berthe_FactoryManager::getInstance($_context);
        $this->_transactionManager = $this->_factoryManager->getInstanceTransaction();
        $this->_init();
    }
    
    protected function _init() {
    }
    
    protected function _onTransactionSet() {
        return true;
    }

    public function setTransaction(Berthe_Modules_Transaction_VO $_transaction) {
        $this->_transaction = $_transaction;
        $_ret = ($this->_transaction instanceof Berthe_Modules_Transaction_VO and $this->_transaction->id > 0);
        $_ret and $_ret = $this->_onTransactionSet();
        return $_ret;
    }
    
    protected function _onEmailSet() {
        return true;
    }

    public function setEmail($email) {
        $this->_email = $email;
        $_ret = Evaneos_Utils_String::isEmail($this->_email);
        $_ret and $_ret = $this->_onEmailSet();
        return $_ret;
    }
    
    public function setAddress($address1, $address2, $zip, $city) {
        return true;
    }
    
    protected function _onValidatePayment() {
        return true;
    }

    protected function _validatePayment() {
        switch ($this->_transaction->status) {
            case Berthe_Modules_Transaction_VO::STATUS_DEACTIVATED :
            case Berthe_Modules_Transaction_VO::STATUS_PAID :
            case Berthe_Modules_Transaction_VO::STATUS_INVALID :
                $_ret = false;
            default :
                $_ret = true;
        }

        $_ret and $_ret = ($this->_transaction->amount > 0);

        $_ret and $_ret = Evaneos_Utils_String::isEmail($this->_email);
        
        $_ret and $_ret = $this->_onValidatePayment();
        
        return $_ret;
    }

    /**
     * Return status by payline code
     * 
     * @param string $code
     * @return integer 
     */
    abstract public function getStatusByReturnCode($code);
    
    abstract public function getErrorMessageByReturnCode($code);
    
    abstract public function getErrorInfosByReturnCode($code);

    abstract protected function _processPayment();
    
    abstract public function getTransactionDetailsByTransaction(Berthe_Modules_Transaction_VO $transaction);

    public function launchPayment() {
        $_ret = $this->_validatePayment();
        if($_ret) {
            return $this->_processPayment();
        } else {
            return $this->_errorHandler;
        }
    }

}