<?php
/**
 * Class definition for Berthe abstract Manager Berthe_Manager
 * 
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Berthe/Manager.php
 * @package Berthe
 */
abstract class Berthe_AbstractManager {
    /**
     * @var Berthe_Context
     */
    public $context = null;
    /**
     *
     * @var Berthe_AbstractStorage
     */
    protected $_storage = null;
    protected $_validator = null;
    /**
     * 
     * @var Berthe_FactoryManager
     */
    protected $_managerFactory = null;
    
    /**
     * @return Berthe_AbstractValidator 
     */
    abstract protected function _getValidator();
    
    /**
     * @return Berthe_AbstractStorage 
     */
    abstract protected function _getStorage();

    /**
     * @return Berthe_AbstractVO[]
     */
    public function getAll() {
        $pagi = new Fetcher(-1, -1);
        $pagi->addSort('id', Fetcher::SORT_ASC);
        return $this->getByPaginator($pagi)->getResultSet();
    }
    
    /**
     * Default method to get an object by its id
     * @param int $id
     * @return Berthe_AbstractVO
     */
    public function getById($id) {
        $_ret = $this->_getStorage()->getById($id);
        return $_ret;
    }
    
    /**
     * Default method to get a list of objects with a list of ids
     * @param array $ids
     * @return Berthe_AbstractVO 
     */
    public function getByIds(array $ids = array()) {
        return $this->_getStorage()->getByIds($ids);
    }
    
    /**
     * @param Fetcher $paginator
     * @return Fetcher 
     */
    public function getByPaginator(Fetcher $paginator) {
        $paginator = $this->_getStorage()->getByPaginator($paginator);
        
        return $paginator;
    }
    
    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher 
     */
    public function getColumnByPaginator(Fetcher $paginator, $columnName = "id") {
        $paginator = $this->_getStorage()->getColumnByPaginator($paginator, $columnName);
        
        return $paginator;
    }
    
    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher 
     */
    public function getColumnByPaginatorPreserveIds(Fetcher $paginator, $columnName = "id") {
        $paginator = $this->_getStorage()->getColumnByPaginatorPreserveIds($paginator, $columnName);
        
        return $paginator;
    }
    
    /**
     * @param Fetcher $paginator
     * @return string sql
     */
    public function getSqlByPaginator($paginator) {
        return $this->_getStorage()->getSqlByPaginator($paginator);
    }
    
    /**
     * 
     * @param Berthe_AbstractVO $vo
     * @param Fetcher $paginator
     * @param int $nbBefore
     * @param int $nbAfter
     * @return array array[voBefore[], voAfter[]]  BEFORE / AFTER 
     */
    public function getNextAndPreviousByPaginator(Berthe_AbstractVO $vo, Fetcher $paginator, $nbBefore = 1, $nbAfter = 1) {
        return $this->_getStorage()->getNextAndPreviousByPaginator($vo, $paginator, $nbBefore, $nbAfter);
    }

    /**
     * Default method to save (insert or update depending on context) an object
     * @param Berthe_AbstractVO $object
     * @return boolean
     */
    public function save($object) { 
        $this->beforeValidate($object);
        $_ret = $this->_getValidator()->validate($object);
        if ($_ret) {
            $this->beforeSave($object);
            $_ret = $this->_getStorage()->save($object);
            $this->afterSave($object);
        }
        return $_ret;
    }
    
    /**
     * @param Berthe_AbstractVO $object
     */
    final protected function beforeValidate($object) {
        // algo framework
        // TODO
        
        // algo business
        $this->_beforeValidate($object);
    }
    
    /**
     * @param Berthe_AbstractVO $object
     */
    protected function _beforeValidate($object) {
        
    }
    
    /**
     * @param Berthe_AbstractVO $object
     */
    final protected function beforeSave($object) {
        // algo framework
        $this->beforeSaveDateHandling($object);
        
        // algo business
        $this->_beforeSave($object);
    }
    
    /**
     * @param Berthe_AbstractVO $object
     */
    private function beforeSaveDateHandling($object) {
        if (property_exists($object, "created_at") && !$object->id) {
            $object->created_at = new DateTime();
        }
        
        if (property_exists($object, "createdAt") && !$object->id) {
            $object->created_at = new DateTime();
        }
        
        if (property_exists($object, "updated_at")) {
            $object->updated_at = new DateTime();
        }
        
        if (property_exists($object, "updatedAt")) {
            $object->updated_at = new DateTime();
        }
    }
    
    /**
     * @param Berthe_AbstractVO $object
     */
    protected function _beforeSave($object) {
        
    }
    
    /**
     * @param Berthe_AbstractVO $object
     */
    final protected function afterSave($object) {
        // algo framework
        // TODO
        
        // algo business
        $this->_afterSave($object);
    }
    
    /**
     * @param Berthe_AbstractVO $object
     */
    protected function _afterSave($object) {
        
    }
    
    /**
     * Default method to delete an object by its id
     * @param int $id
     * @return boolean 
     */
    public function deleteById($id) {
        $_object = $this->getById($id);
        $this->_onBeforeDelete($_object);
        $_ret = $this->_getStorage()->deleteById($id);
        $this->_onAfterDelete($_object);
        return $_ret;
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    protected function _onBeforeDelete(Berthe_AbstractVO $_object) {
        return;
    }

    protected function _onAfterDelete(Berthe_AbstractVO $_object) {
        return;
    }

    abstract public function getVoForCreation();
    
    /**
     * Return an array with the properties for the given set of Berthe VO
     * @param string $property (can dig into object using dots (exemple :  propObject.subpropObject.prop))
     * @param array $vos 
     * @return array key=vo->id, value=vo->property
     */
    public function extractProperty($property, array $vos = array()) {
        $propertyChain = explode(".", $property);

        $ret = array();
        foreach($vos as /* @var $vo Berthe_AbstractVO */ $vo) {
            $voChainable = $vo;
            $copyChain = $propertyChain;
            if (!($voChainable instanceof Berthe_AbstractVO)) {
                trigger_error("Wrong object given for property extraction");
                continue;
            }
            while(($prop = array_shift($copyChain)) !== null) {
                if (property_exists($voChainable, $prop)) {
                    $voChainable = $voChainable->{$prop};
                }
            }
            
            $ret[$vo->id] = $voChainable;
        }
        return $ret;
    }
    
     /**
     * Return an array with the properties for the given set of Berthe VO
     * @param string $property 
     * @param array $vos 
     * @return array ($vo->id, $vo->property)
     */
    public function extractPropertyInArray($property, array $vos = array()) {
        $ret = array(); 
        foreach($vos as /* @var $vo Berthe_AbstractVO */ $vo) {
            if (!($vo instanceof Berthe_AbstractVO)) {
                trigger_error("Wrong object given for property extraction");
                continue;
            }
            if (property_exists($vo, $property)) {
                $ret[] = array(
                    'id' => $vo->id,
                    $property => $vo->{$property}
                );
            }
        }
        return $ret;
    }
    
    /**
     *
     * @param string $property
     * @param array $vos
     * @return array 
     */
    public function sortByProperty($property, array $vos = array()) {
        $extract = $this->extractProperty($property, $vos);
        asort($extract);
        $ret = array();
        foreach(array_keys($extract) as $idSibling) {
            $ret[$idSibling] = $vos[$idSibling];
        }
        return $ret;
    }
    
    /**
     * Forces the cache reload of an object
     * @param integer $id
     * @deprecated
     */
    public function reloadById($id) {
        trigger_error('Do not use reload method in Berthe model!', E_USER_DEPRECATED);
        $_storage = $this->_getStorage();
        $_oldValue = $_storage->ignoreCache;
        $_storage->ignoreCache = true;
        $_res = $_storage->getById($id);
        $_storage->ignoreCache = $_oldValue;
        return $_res;
    }
    
    /**
     * Forces the cache reload of a bunch of objects
     * @param integer $id
     * @deprecated
     */
    public function reloadByIds(array $ids) {
        trigger_error('Do not use reload method in Berthe model!', E_USER_DEPRECATED);
        $_storage = $this->_getStorage();
        $_oldValue = $_storage->ignoreCache;
        $_storage->ignoreCache = true;
        $_res = $_storage->getByIds($ids);
        $_storage->ignoreCache = $_oldValue;
        return $_res;
    }

    public function ignoreAllCache($shallIgnore = null){
        $this->_getStorage()->ignoreAllCache($shallIgnore);
    }
}