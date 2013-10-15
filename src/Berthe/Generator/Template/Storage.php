<?php
class Berthe_Generator_Template_Storage extends Berthe_Generator_Template_Abstract {
    const CLASSNAME = "DAOStorage";
    
    protected static $_instance = null;
    /**
     *
     * @return Berthe_Generator_Template_Storage 
     */
    public static function getInstance() {
        self::$_instance === null && self::$_instance = new static();
        return self::$_instance;
    }
    
    protected function _getClassName() {
        return self::CLASSNAME;
    }
    
    protected function _loadSignature(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setExtendedClass("Berthe_AbstractStorage");
        
        parent::_loadSignature($zcg);
    }
        
    protected function _loadConsts(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setProperty(array("name" => "MEMCACHED_NAME", 
                        "static" => false, 
                        "const" => true, 
                        "defaultValue" => strtolower($this->_getPackageName($zcg->getName()))
                        ));
        
        parent::_loadConsts($zcg);
    }
        
    protected function _loadProperties(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setProperty(array("name" => "_reader", 
                                "static" => false, 
                                "const" => false, 
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED,
                                "defaultValue" => null,
                                "docBlock" => array(
                                    "tag" => array(
                                        "name" => "return",
                                        "description" => $this->_getPackageName($zcg->getName()) . "_DAOReader"
                                    )
                                )
                            ));
                
        $zcg->setProperty(array("name" => "_writer", 
                                "static" => false, 
                                "const" => false, 
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED,
                                "defaultValue" => null,
                                "docBlock" => array(
                                    "tag" => array(
                                        "name" => "return",
                                        "description" => $this->_getPackageName($zcg->getName()) . "_DAOWriter"
                                    )
                                )
                            ));
        
        parent::_loadProperties($zcg);
    }
        
    protected function _loadMethods(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setMethod(array("name" => "_initDatabaseConnections",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED,
                                "docBlock" => array(
                                    "shortDescription" => "Get the database connections"
                                ),
                                "body" => <<<PHPCODE
\$this->_reader = new {$this->_getPackageName($zcg->getName())}_DAOReader(\$this->context);
\$this->_writer = new {$this->_getPackageName($zcg->getName())}_DAOWriter(\$this->context);
PHPCODE
        ));
        
        $zcg->setMethod(array("name" => "memcachedName",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED,
                                "docBlock" => array(
                                    "shortDescription" => "Return the memcached unique name"
                                ),
                                "body" => <<<PHPCODE
return self::MEMCACHED_NAME;
PHPCODE
        ));
        
        parent::_loadMethods($zcg);
    }
}