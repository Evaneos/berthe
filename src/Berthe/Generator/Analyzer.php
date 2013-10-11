<?php
abstract class Evaneos_Berthe_Generator_Analyzer {
    public $loggerOK = null;
    public $loggerKO = null;
    
    protected $isOK = true;
    
    public function analyze(Zend_CodeGenerator_Php_Class $zcg) {
        $this->isOK = true;
        $this->_analyze($zcg);
        
        return $this->isOK;
    }
    
    protected function logKO($msg) {
        $this->loggerKO->log($msg);
        $this->isOK = false;
    }
    
    protected function logOK($msg) {
        $this->loggerOK->log($msg);
    }
    
    abstract protected function _analyze(Zend_CodeGenerator_Php_Class $zendClass);
}