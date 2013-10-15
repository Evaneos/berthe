<?php
class Berthe_Generator_AnalyzerLength extends Berthe_Generator_Analyzer {
    protected $sizeCheck = 30;
    
    public function __construct($size = 30) {
        $this->sizeCheck = $size;
    }
    
    public function setSizeCheck($size = 30) {
        $this->sizeCheck = $size;
        return $this;
    }
    
    protected function _analyze(Zend_CodeGenerator_Php_Class $zendClass) {
        $methods = $zendClass->getMethods();
        foreach($methods as /* @var $method Zend_CodeGenerator_Php_Method */ $method) {
            $nbLines = count(explode("\n", $method->getBody()));
            if ($nbLines > $this->sizeCheck) {
                $this->logKO($zendClass->getName() . '::' . $method->getName() . '()' . " too large (" . $nbLines . " lines, expected < $this->sizeCheck)");
            }
            else {
                $this->logOK($zendClass->getName() . '::' . $method->getName() . '()' . " OK");
            }
        }
    }
}