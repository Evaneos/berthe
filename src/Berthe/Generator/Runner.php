<?php
class Evaneos_Berthe_Generator_Runner {
    protected $analyzers = array();
    protected $classNames = array();
    public $loggerOK = null;
    public $loggerKO = null;
    
    public function __construct() {
        $this->loggerKO = new Log_Logger();
        $this->loggerOK = new Log_Logger();
    }
    
    public function addAnalyzer(Evaneos_Berthe_Generator_Analyzer $analyzer) {
        $this->analyzers[] = $analyzer;
        $analyzer->loggerOK = $this->loggerOK;
        $analyzer->loggerKO = $this->loggerKO;
        return $this;
    }
    
    public function setClassesToAnalyze(array $classNames = array()) {
        $this->classNames = $classNames;
        return $this;
    }
    
    public function addClassToAnalyze($className) {
        if (class_exists($className) && !in_array($className, $this->classNames)) {
            $this->classNames[] = $className;
        }
        return $this;
    }
    
    public function run() {
        foreach($this->classNames as $className) {
            try {
                $zendClass = Zend_CodeGenerator_Php_Class::fromReflection(new Zend_Reflection_Class($className));
                
                foreach($this->analyzers as $analyzer) {
                    try {
                        $analyzer->analyze($zendClass);
                    }
                    catch (Exception $e) {
                        $this->loggerKO->log("Analyzer error sur la class " . $className . " car " .$e->getMessage());
                    }
                }
            } 
            catch (Exception $e) {
                $this->loggerKO->log("IMPOSSIBLE DE PARSER LA CLASS " . $className . " car " .$e->getMessage());
            }
        }
    }
}