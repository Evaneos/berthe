<?php
class Berthe_Generator_Template_Manager extends Berthe_Generator_Template_Abstract {
    const CLASSNAME = "Manager";
    
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
        $zcg->setExtendedClass("Berthe_AbstractManager");
        parent::_loadSignature($zcg);
    }
    
    protected function _loadExtra(Zend_CodeGenerator_Php_Class $zcg) {
        //$this->_loadSingleton($zcg);
        parent::_loadExtra($zcg);
    }
    
    protected function _loadMethods(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setMethod(array("name" => "_getValidator",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED,
                                "docBlock" => array(
                                    "tag" => array(
                                        "name" => "return",
                                        "description" => $this->_getPackageName($zcg->getName()) . '_Validator'
                                    )
                                ),
                                "body" => <<<PHPCODE
is_null(\$this->_validator) && \$this->_validator = new {$this->_getPackageName($zcg->getName())}_Validator();
return \$this->_validator;
PHPCODE
        ));

        $zcg->setMethod(array("name" => "_getStorage",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED,
                                "docBlock" => array(
                                    "tag" => array(
                                        "name" => "return",
                                        "description" => $this->_getPackageName($zcg->getName()) . '_DAOStorage'
                                    )
                                ),
                                "body" => <<<PHPCODE
is_null(\$this->_storage) && \$this->_storage = new {$this->_getPackageName($zcg->getName())}_DAOStorage(\$this->context);
return \$this->_storage;
PHPCODE
        ));

        $zcg->setMethod(array("name" => "getVoForCreation",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "docBlock" => array(
                                    "tag" => array(
                                        "name" => "return",
                                        "description" => $this->_getPackageName($zcg->getName()) . '_VO'
                                    )
                                ),
                                "body" => <<<PHPCODE
\$vo = new {$this->_getPackageName($zcg->getName())}_VO();
return \$vo;
PHPCODE
        ));

        parent::_loadMethods($zcg);
    }
}