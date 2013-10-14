<?php
class Berthe_Generator_Util {
    static $isTestMode = false;
    
    /**
     * Generate default Berthe files for given package
     * WARNING : Overwrites files if exist
     * 
     * @param string $fullName 
     */
    public static function createStandardBerthePackage($fullName) {
        if (!is_string($fullName) || strlen(trim($fullName)) === 0 || strpos($fullName, "Berthe") !== false) {
                trigger_error("The package name must be a string and exclude main 'Berthe' package (valid example : Article/Module/Type) ");
        }
        self::_createPackage("Berthe/" . $fullName);
    }
    
    protected static function _getFileNameAccordingAutoload($className) {
        $sFileName = str_replace('_', '/', $className);
        $_fp = explode("/", $sFileName);
        $count = count($_fp);
        $sFileName = $_fp[$count-2] . $_fp[$count-1] . ".php";
        
        return $sFileName;
    }
    
    protected static function _filenameFromBertheToClassname($filename) {
        if (preg_match("`.*(Berthe/.*)\.php`i", $filename, $result)) {
            $classPath = $result[1];
            $classPath = str_replace("/", "_", $classPath);
            $aClassPath = explode("_", $classPath);
            $finalPart = array_pop($aClassPath);
            array_push($aClassPath, substr($finalPart, strlen($aClassPath[count($aClassPath)-1])));
            return implode("_", $aClassPath);
        }
        else {
            trigger_error("not a berthe filename");
            return false;
        }
    }
    
    /**
     *
     * @param type $fullName
     * @param type $name 
     */
    protected static function _createPackage($fullName) {
        $classes = array();
        $manager = Berthe_Generator_Template_Manager::getInstance()->get($fullName);
        $classes[] = $manager;
        $classes[] = Berthe_Generator_Template_VO::getInstance()->get($fullName);
        $classes[] = Berthe_Generator_Template_Storage::getInstance()->get($fullName);
        $classes[] = Berthe_Generator_Template_Reader::getInstance()->get($fullName);
        $classes[] = Berthe_Generator_Template_Writer::getInstance()->get($fullName);
        $classes[] = Berthe_Generator_Template_Validator::getInstance()->get($fullName);
        
        $folder = ROOT_DIR . '/app/' . $fullName . "/";
        
        $_res = true;
        if (!is_dir($folder)) {
            self::$isTestMode === false && $_res = mkdir($folder, 0766, true);
        }
        
        if ($_res === true) {
            foreach($classes as /* @var $class Zend_CodeGenerator_Php_Class */ $class) {
                $zendFile = new Zend_CodeGenerator_Php_File();
                $zendFile->setClass($class);

                $fileName = self::_getFileNameAccordingAutoload($class->getName());
                $filePath = $folder . $fileName;
                $content = $zendFile->generate();
                echo $filePath . "<br />\n" . $content . "<br /><br />\n\n";
                $test = self::$isTestMode;
                self::$isTestMode === false && $_res = file_put_contents($filePath, $content);
                if ($_res === false) {
                    trigger_error("Couldn't write file, file_put_contents() failed for '" . $filePath . "'");
                }
            }
            
            if ($_res === false) {
                trigger_error("Couldn't write factorymanager file, file_put_contents() failed");
            }
        }
        else {
            trigger_error("Couldn't create folder for file generation, mkdir() failed for '".$folder."'");
        }
    }
    
    public static function _reconstructManagerWithRecursiveParsing() {
        $files = self::_recursiveReaddir(ROOT_DIR . '/app/Berthe');
        $manager = new Zend_CodeGenerator_Php_Class();
        $manager->setName("Berthe_FactoryManager");

        
        
        
        
        
        $manager->setProperty(array("name" => "context", 
                        "static" => false, 
                        "defaultValue" => null, 
                        "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PRIVATE,
                        "docBlock" => array(
                            "tag" => array(
                                "name" => "var",
                                "description" => 'Berthe_Context'
                            )
                        )));

        $manager->setMethod(array("name"=> "__construct",
                                    "docBlock" => array(
                                        "shortDescription" => "Constructor"
                                    ),
                                    "parameter" => array(
                                        "name" => "context",
                                        "defaultValue" => null,
                                        "type" => "Berthe_Context"
                                    ),
                                "body" => <<<PHPCODE
\$this->context = \$context;
PHPCODE
));

        $manager->setMethod(array(  "name"=> "prepareInstance",
                                    "visibility" => Zend_CodeGenerator_Php_Member_Abstract::VISIBILITY_PROTECTED,
                                    "docBlock" => array(
                                        "shortDescription" => "Prepare the manager with context data, interceptors, ..."
                                    ),
                                    "parameter" => array(
                                        "name" => "manager",
                                        "type" => "Berthe_AbstractManager"
                                    ),
                                "body" => <<<PHPCODE
\$manager->context = \$this->context;
return \$manager; 
PHPCODE
));
        
        
        $filegenerator = new Zend_CodeGenerator_Php_File();
        $filegenerator->setClass($manager);
        
        foreach($files as $file) {
            $classname = self::_filenameFromBertheToClassname($file);
            if (preg_match("`.*_Manager$`i", $classname)) {
                $managerClass = new Zend_Reflection_Class($classname);
                $aClassname = explode("_", $managerClass->getName());
                
                
                $className = implode("_", $aClassname);
                
                array_pop($aClassname);
                
                $packagePascalCase = implode("", $aClassname);
                $packagePascalCase = str_replace("BertheModules", "", $packagePascalCase);
                
                $manager->setMethod(array("name"=> "getInstance" . $packagePascalCase,
                                    "static" => true,
                                    "docBlock" => array(
                                        "tags" => array(array(
                                            "name" => "return",
                                            "description" => $className
                                        ))
                                    ),
                                "body" => <<<PHPCODE
return \$this->prepareInstance(new {$className});
PHPCODE
));
                
            }
        }
        
        $content = $filegenerator->generate();
        echo $content . "<br /><br />";
        
        if (self::$isTestMode === true) {
            return;
        }
        else {
            $fileManager = ROOT_DIR . '/app/Berthe/BertheFactoryManager.php';
            file_put_contents($fileManager, $content);
            
        }
        

    }
    
    protected static function _recursiveReaddir($dirpath) {
        $return = array();
        
        $rDir = opendir($dirpath);
        while($path = readdir($rDir)) {
            if ($path == '.' || $path == '..') {
                continue;
            }
            
            if (is_dir($dirpath . '/' . $path)) {
                $return = array_merge($return, self::_recursiveReaddir($dirpath . '/' . $path));
            }
            else {
                $return[] = $dirpath . '/' . $path;
            }
        }
        
        return $return;
    }
    
    protected static function _addToManager($package, $manager) {
        $class = Zend_CodeGenerator_Php_Class::fromReflection(new Zend_Reflection_Class("Berthe_FactoryManager"));
        $package = str_replace("/", "", $package);
        $package = str_replace("_", "", $package);
        
        if($class->hasMethod("getInstance" . $package)) {
            trigger_error("Factory method already exists");
        }
        else {
            $class->setMethod(array("name"=> "getInstance" . $package,
                                    "docBlock" => array(
                                        "tags" => array(array(
                                            "name" => "return",
                                            "description" => $manager->getName()
                                        ))
                                    ),
                                "body" => <<<PHPCODE
PROFILER and \$_dbg = Profiler::startProfile(__CLASS__, __FUNCTION__);

if (!array_key_exists('{$package}', self::\$_instances)) {
    \$_class = {$manager->getName()}::getInstance();
    self::\$_instances['{$package}'] = \$_class;
}

PROFILER and Profiler::endProfile(\$_dbg);

return self::\$_instances['{$package}'];
PHPCODE
));
        }
        
        return new Zend_CodeGenerator_Php_File(array("class"=>$class));
    }
    
    /**
     * @param mixed $class
     * @return null|Zend_CodeGenerator_Php_Class 
     */
    protected static function _transformInZCG($class) {
        switch(1) {
            case is_string($class) :
                $refClass = new Zend_Reflection_Class($class);
                $zcg = Zend_CodeGenerator_Php_Class::fromReflection($refClass);
                break;
            case ($class instanceof Zend_Reflection_Class) :
                $zcg = Zend_CodeGenerator_Php_Class::fromReflection($class);
                break;
            case ($class instanceof Zend_CodeGenerator_Php_Class) :
                $zcg = $class;
                break;
            default :
                trigger_error("class must be either a string, a reflection class or a codegenerator class");
                return null;
                break;
        }
        return $zcg;
    }
    
    public static function merge($class1, $class2) {
        $zcg1 = self::_transformInZCG($class1);
        $zcg2 = self::_transformInZCG($class2);

        if ($zcg1 === null || $zcg2 === null) {
            trigger_error("At least one class is null after convert into codegenerator");
            return null;
        }
        
        foreach($zcg1->getMethods() as $key => /* @var $method Zend_CodeGenerator_Php_Method */ $method) {
            if ($zcg2->hasMethod($key)) {
                echo "matching method " . $key ."<br />\n";
            }
            else {
                echo "method not found " . $key ."<br />\n";
            }
        }
        
        foreach($zcg1->getProperties() as $key => /* @var $method Zend_CodeGenerator_Php_Properties */ $properties) {
            if ($zcg2->hasProperty($key)) {
                if ($zcg1->getProperty($key) == $zcg2->getProperty($key)) {
                    echo "matching property " . $key ."<br />\n";
                }
                else {
                    var_dump($zcg1->getProperty($key));
                    var_dump($zcg2->getProperty($key));
                    echo "not same signature for property " . $key . "<br />\n";
                }
                
            }
            else {
                echo "property not found " . $key ."<br />\n";
            }
        }
        
        echo "<br /><br />\n\n";
        
        foreach($zcg2->getMethods() as $key => /* @var $method Zend_CodeGenerator_Php_Method */ $method) {
            if ($zcg1->hasMethod($key)) {
                echo "matching method " . $key ."<br />\n";
            }
            else {
                echo "method not found " . $key ."<br />\n";
            }
        }
        
        foreach($zcg2->getProperties() as $key => /* @var $method Zend_CodeGenerator_Php_Properties */ $properties) {
            if ($zcg1->hasProperty($key)) {
                echo "matching property " . $key ."<br />\n";
            }
            else {
                echo "property not found " . $key ."<br />\n";
            }
        }
	
	
    }
}