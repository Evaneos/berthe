<?php
abstract class Evaneos_Berthe_Generator_Template_Abstract {
    final protected function __construct() {}
    final protected function __clone() {}
    
    abstract protected function _getClassName();
    
    protected function _loadSignature(Zend_CodeGenerator_Php_Class $zcg) {
        
    }
    protected function _loadConsts(Zend_CodeGenerator_Php_Class $zcg) {
        
    }
    protected function _loadProperties(Zend_CodeGenerator_Php_Class $zcg) {
        
    }
    protected function _loadMethods(Zend_CodeGenerator_Php_Class $zcg) {
        
    }
    protected function _loadExtra(Zend_CodeGenerator_Php_Class $zcg) {
        
    }
    
    /**
     * Return a Zend_CodeGenerator_Php_Class 
     * @param string $packageName 
     * @return Zend_CodeGenerator_Php_Class
     */
    public function get($packageName) {
        $package = $this->_formatPackage($packageName);
        return $this->_get($this->_loadBaseClass($package, $this->_getClassName()));
    }
    
    protected function _get(Zend_CodeGenerator_Php_Class $zcg) {
        $this->_loadSignature($zcg);
        $this->_loadConsts($zcg);
        $this->_loadProperties($zcg);
        $this->_loadMethods($zcg);
        $this->_loadExtra($zcg);
        
        return $zcg;
    }
    
    /**
     * @param string $package
     * @return string 
     */
    protected function _formatPackage($package) {
        if (!is_string($package) || strlen(trim($package)) === 0) {
            trigger_error("The package name must be a string (valid example : Berthe/Article/Module/Type) ");
            return null;
        }
        
        return str_replace("/", "_", $package);
    }
    
    protected function _getPackageName($className) {
        $_a = explode("_", $className);
        array_pop($_a);
        return implode("_", $_a);
    }

    /**
     * @return Zend_CodeGenerator_Php_Class 
     */
    protected function _loadBaseClass($packageName, $className) {
        $zcg = new Zend_CodeGenerator_Php_Class();
        $zcg->setName($packageName . "_" . $className);
        $this->_loadClassDocBlock($zcg);
        
        return $zcg;
    }
    
    /**
     * Set the doc block for the given class
     * @param Zend_CodeGenerator_Php_Class $zcg 
     */
    protected function _loadClassDocBlock(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setDocblock(array(
            "shortDescription" => "Class definition for " . $zcg->getName(),
            "tags" => array(
                array("name" => "copyright", "description" => "Evaneos"),
                array("name" => "author", "description" => Zend_Registry::get("siteMails")->dev),
                array("name" => "generated", "description" => date("d-m-Y H:i:s")),
                array("name" => "version", "description" => "1.0"),
                array("name" => "since", "description" => "Berthe")
            )
        ));
    }
    
    protected function _loadSingleton(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setProperty(array("name" => "_instance", 
                                "static" => true, 
                                "defaultValue" => null, 
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED,
                                "docBlock" => array(
                                    "tag" => array(
                                        "name" => "var",
                                        "description" => $zcg->getName()
                                    )
                                )));
        
        $zcg->setMethod(array("name" => "getInstance",
                                "static" => true,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "docBlock" => array(
                                    "tag" => array(
                                        "name" => "return",
                                        "description" => $zcg->getName()
                                    )
                                ),
                                "body" => <<<PHPCODE
is_null(self::\$_instance) and self::\$_instance = new static();
return self::\$_instance;
PHPCODE
        ));
    }
}