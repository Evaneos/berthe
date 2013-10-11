<?php
abstract class Evaneos_Berthe_AbstractValidator {
    /**
     * @var Evaneos_Berthe_ErrorHandler 
     */
    protected $_errorHandler = null;
    /**
     * @var Evaneos_Berthe_AbstractManager 
     */
    protected $manager = null;
    
    protected $baseErrorConstant = 0;
    
    public function __construct(Evaneos_Berthe_AbstractManager $manager = null) {
        $this->manager = $manager;
        $this->_errorHandler = new Evaneos_Berthe_ErrorHandler('Error handler', 0);

        $this->initBaseErrorConstant();
    }
    
    /**
     * OVERRIDE THIS TO HANDLE PROPERLY ERROR CODES
     */
    protected function initBaseErrorConstant() {
        $this->baseErrorConstant = Evaneos_Berthe_ErrorHandler::CONST_START_GENERIC;
    }
    
    /**
     * @param int $errorCode
     * @return int
     */
    protected function getErrCode($errorCode) {
        return $this->baseErrorConstant + $errorCode;
    }
    
    /**
     * @return Evaneos_Berthe_ErrorHandler 
     */
    public function getErrors() {
        return $this->_errorHandler;
    }
    
    
}