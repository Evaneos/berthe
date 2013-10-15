<?php
class Berthe_Generator_Template_VO extends Berthe_Generator_Template_Abstract {
    const CLASSNAME = "VO";
    
    protected static $_instance = null;
    /**
     *
     * @return Berthe_Generator_Template_Manager 
     */
    public static function getInstance() {
        self::$_instance === null && self::$_instance = new static();
        return self::$_instance;
    }
    
    protected function _getClassName() {
        return self::CLASSNAME;
    }
    
    protected function _loadSignature(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setExtendedClass("Berthe_AbstractVO");
        
        parent::_loadSignature($zcg);
    }
    
    protected function _loadProperties(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setProperty(array("name" => "VERSION", 
                                "static" => false, 
                                "const" => true, 
                                "defaultValue" => 1
                                ));
        
        $zcg->setProperty(array("name" => "id", 
                                "static" => false, 
                                "const" => false, 
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "defaultValue" => 0
                                ));
        
        parent::_loadProperties($zcg);
    }
}