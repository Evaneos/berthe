<?php
class Berthe_Generator_Template_Validator extends Berthe_Generator_Template_Abstract {
    const CLASSNAME = "Validator";
    
    protected static $_instance = null;
    /**
     *
     * @return Berthe_Generator_Template_Validator
     */
    public static function getInstance() {
        self::$_instance === null && self::$_instance = new static();
        return self::$_instance;
    }
    
    protected function _getClassName() {
        return self::CLASSNAME;
    }
    
    protected function _loadSignature(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setImplementedInterfaces(array("Berthe_Validator"));
        parent::_loadSignature($zcg);
    }
        
    protected function _loadMethods(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setMethod(array("name" => "validate",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "docBlock" => array(
                                    "tags" => array(array(
                                        "name" => "param",
                                        "description" => 'mixed $object'
                                    ), array(
                                        "name" => "return",
                                        "description" => "boolean"
                                    )
                                )),
                                "parameter" => array(
                                    "name" => "object"
                                ),
                                "body" => <<<PHPCODE
\$valid = true;
return \$valid;
PHPCODE
        ));
        
        parent::_loadMethods($zcg);
    }
}