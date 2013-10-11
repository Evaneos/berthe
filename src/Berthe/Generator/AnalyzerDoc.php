<?php
class Evaneos_Berthe_Generator_AnalyzerDoc extends Evaneos_Berthe_Generator_Analyzer {
    protected function _analyze(Zend_CodeGenerator_Php_Class $zendClass) {
        $methods = $zendClass->getMethods();
        foreach($methods as $method) {
            $nbParameters = count($method->getParameters());
            if ($method->getDocblock() == null) {
                $this->logKO($zendClass->getName() . '::' . $method->getName() . '()' . " has no PHPDoc");
            }
            else {
                $docBlock = $method->getDocblock();
                if ($docBlock instanceof Zend_CodeGenerator_Php_Docblock) {
                    $tags = $docBlock->getTags();
                    $count = 0;
                    foreach($tags as $tag) {
                        if ($tag instanceof Zend_CodeGenerator_Php_Docblock_Tag_Param) {
                            $count++;
                        }
                    }

                    if ($count != $nbParameters) {
                        $this->logKO($zendClass->getName() . '::' . $method->getName() . '()' . " mismatch doc signature ==> " . $count . " out of " . $nbParameters);
                    }
                    else {
                        $this->logOK($zendClass->getName() . '::' . $method->getName() . '()' . " OK");
                    }
                }
            }
        }
    }
}