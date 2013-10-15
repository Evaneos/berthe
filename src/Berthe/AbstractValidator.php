<?php
abstract class Berthe_AbstractValidator {
    /**
     * @var Berthe_ErrorHandler 
     */
    protected $_errorHandler = null;
    /**
     * @var Berthe_AbstractManager 
     */
    protected $manager = null;
    
    protected $baseErrorConstant = 0;
    
    public function __construct(Berthe_AbstractManager $manager = null) {
        $this->manager = $manager;
        $this->_errorHandler = new Berthe_ErrorHandler('Error handler', 0);

        $this->initBaseErrorConstant();
    }
    
    /**
     * OVERRIDE THIS TO HANDLE PROPERLY ERROR CODES
     */
    protected function initBaseErrorConstant() {
        $this->baseErrorConstant = Berthe_ErrorHandler::CONST_START_GENERIC;
    }
    
    /**
     * @param int $errorCode
     * @return int
     */
    protected function getErrCode($errorCode) {
        return $this->baseErrorConstant + $errorCode;
    }
    
    /**
     * @return Berthe_ErrorHandler 
     */
    public function getErrors() {
        return $this->_errorHandler;
    }
    
    
}