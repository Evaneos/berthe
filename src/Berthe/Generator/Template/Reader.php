<?php
class Evaneos_Berthe_Generator_Template_Reader extends Evaneos_Berthe_Generator_Template_Abstract {
    const CLASSNAME = "DAOReader";
    
    protected static $_instance = null;
    /**
     *
     * @return Evaneos_Berthe_Generator_Template_Reader
     */
    public static function getInstance() {
        self::$_instance === null && self::$_instance = new static();
        return self::$_instance;
    }
    
    protected function _getClassName() {
        return self::CLASSNAME;
    }
    
    protected function _loadConsts(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setProperty(array("name" => "TABLENAME", 
                        "static" => false, 
                        "const" => true, 
                        "defaultValue" => strtolower($this->_getPackageName($zcg->getName()))
                        ));
        
        parent::_loadConsts($zcg);
    }
    
    protected function _loadSignature(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setExtendedClass("Evaneos_Berthe_AbstractReader");
        
        parent::_loadSignature($zcg);
    }
        
    protected function _loadMethods(Zend_CodeGenerator_Php_Class $zcg) {
        
        $zcg->setMethod(array("name" => "getTableName",
                                "static" => false,
                                "docBlock" => array(
                                    "shortDescription" => "Return the table name of the table.",
                                    "tags" => array(array(
                                        "name" => "return",
                                        "description" => "string"
                                    ))
                                ),
                                "body" => <<<PHPCODE
return self::TABLENAME;
PHPCODE
                ));
        
        $zcg->setMethod(array("name" => "selectByIds",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "docBlock" => array(
                                    "shortDescription" => "Return an array of " . $this->_getPackageName($zcg->getName()) . "_VO",
                                    "tags" => array(array(
                                        "name" => "param",
                                        "description" => 'array $ids'
                                    ),array(
                                        "name" => "return",
                                        "description" => $this->_getPackageName($zcg->getName()) . "_VO[]"
                                    ))
                                ),
                                "parameter" => array(
                                    "name" => "ids",
                                    "type" => "array",
                                    "defaultValue" => array()
                                ),
                                "body" => <<<PHPCODE
\$_ret = array();
\$implode = implode(', ', \$ids);
// TODO write sql code
\$sql = "";
\$resultSet = \$this->_db->fetchAll(\$sql);
foreach(\$resultSet as &\$row) {
    \$_ret[\$row['id']] = new {$this->_getPackageName($zcg->getName())}_VO(\$row);
}
return \$_ret;
PHPCODE
        ));
        parent::_loadMethods($zcg);
    }
}