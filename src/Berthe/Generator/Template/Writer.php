<?php
class Berthe_Generator_Template_Writer extends Berthe_Generator_Template_Abstract {
    const CLASSNAME = "DAOWriter";
    
    protected static $_instance = null;
    /**
     *
     * @return Berthe_Generator_Template_Writer
     */
    public static function getInstance() {
        self::$_instance === null && self::$_instance = new static();
        return self::$_instance;
    }
    
    protected function _getClassName() {
        return self::CLASSNAME;
    }
    
    protected function _loadSignature(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setExtendedClass("Berthe_AbstractWriter");
        
        parent::_loadSignature($zcg);
    }
        
    protected function _loadMethods(Zend_CodeGenerator_Php_Class $zcg) {
        $zcg->setMethod(array("name" => "delete",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "docBlock" => array(
                                    "shortDescription" => "Delete an object (is_deleted flag setted to 1)",
                                    "tags" => array(array(
                                        "name" => "param",
                                        "description" => 'Berthe_AbstractVO $object'
                                    ))
                                ),
                                "parameter" => array(
                                    "name" => "object",
                                    "type" => "Berthe_AbstractVO"
                                ),
                                "body" => <<<PHPCODE


PHPCODE
        ));
        
        $zcg->setMethod(array("name" => "deleteById",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "docBlock" => array(
                                    "shortDescription" => "Delete an object by id (is_deleted flag setted to 1)",
                                    "tags" => array(array(
                                        "name" => "param",
                                        "description" => 'int $id'
                                    ))
                                ),
                                "parameter" => array(
                                    "name" => "id"
                                ),
                                "body" => <<<PHPCODE
   
   
PHPCODE
        ));
                
        $zcg->setMethod(array("name" => "insert",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "docBlock" => array(
                                    "shortDescription" => "Insert an object",
                                    "tags" => array(array(
                                        "name" => "param",
                                        "description" => 'Berthe_AbstractVO $object'
                                    ))
                                ),
                                "parameter" => array(
                                    "name" => "object",
                                    "type" => "Berthe_AbstractVO"
                                ),
                                "body" => <<<PHPCODE
\$sql = "
INSERT INTO 
    tablename
    (
    column_1,
    column_2,
    column_3
    )
VALUES
    (?, ?, ?)
";

\$_today = new DateTime();
\$this->_db->query(\$sql, array(
    \$object->column_1,
    \$object->column_2,
    \$object->column_3
));

\$id = \$this->_db->lastInsertId("tablename", "id");
if (\$id > 0) {
    \$object->id = \$id;
    return true;
}
else {
    return false;
}
PHPCODE
        ));
                
        $zcg->setMethod(array("name" => "update ",
                                "static" => false,
                                "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PUBLIC,
                                "docBlock" => array(
                                    "shortDescription" => "Update an object",
                                    "tags" => array(array(
                                        "name" => "param",
                                        "description" => 'Berthe_AbstractVO $object'
                                    ))
                                ),
                                "parameter" => array(
                                    "name" => "object",
                                    "type" => "Berthe_AbstractVO"
                                ),
                                "body" => <<<PHPCODE
\$sql = "
UPDATE
    tablename
SET 
    column_1 = ?,
    column_2 = ?,
    column_3 = ?
WHERE
    id = ?
";

\$_today = new DateTime();
\$this->_db->query(\$sql, array(
    \$object->column_1,
    \$object->column_2,
    \$object->column_3,
    \$object->id
));

return true;
PHPCODE
        ));
        
        
        parent::_loadMethods($zcg);
    }
}