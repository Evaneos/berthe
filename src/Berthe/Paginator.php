<?php

/**
 * Class definition for VO Evaneos_Berthe_Paginator
 * 
 * @author anthony@evaneos.Com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Berthe/Package/VO.php
 * @package Berthe/Package
 * @since Berthe
 */
class Evaneos_Berthe_Paginator implements ArrayAccess {
    // Properties
    /**
     * Filters set
     * @var array
     */
    protected $_elements = array();
    protected $_page = 1;
    protected $_nbByPage = 25;
    protected $_ttlCount = 0;
    private $_count = 0;
    
    /**
     * Checks if an offset exists
     * @param string $offset
     * @return boolean 
     */
    public function offsetExists($offset) {
        return isset($this->_elements[$offset]);
    }
    
    /**
     * Gets an offset
     * @param type $offset
     * @return type 
     */
    public function offsetGet($offset) {
        return is_int($offset) ? $this->_elements[$offset] : null;
    }
    
    /**
     * sets an offset
     * @param type $offset
     * @param type $value 
     */
    public function offsetSet($offset, $value) {
        !is_int($offset) and trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Only accepts integer offsets', E_USER_WARNING);
        ($this->_count >= $this->_nbByPage) and trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Max number of elements by page reached', E_USER_ERROR);
        if(is_int($offset) and ($this->hasLimit() && ($this->_count >= $this->_nbByPage))) {
            $this->_elements[$offset] = $value;
            $this->_count = count($this->_elements);
        }
    }
    
    /**
     * Unsets an offset
     * @param type $offset 
     */
    public function offsetUnset($offset) {
        !is_int($offset) and trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Only accepts integer offsets', E_USER_WARNING);
        if(is_int($offset) and isset($this->_elements[$offset])) {
            unset($this->_elements[$offset]);
            $this->_count = count($this->_elements);
        }
    }
    
    /**
     * Sets the datas of $array to the elements
     * @param array $array
     * @param boolean $preserveIds
     * @return type 
     */
    public function set(array $array = array(), $preserveIds = false) {
        if($this->hasLimit() and ($this->_nbByPage >= 0 and count($array) > $this->_nbByPage)) {
            trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Max number of elements by page reached', E_USER_ERROR);
            return false;
        }
        
        if ($preserveIds) {
            $this->_elements = $array; 
        }
        else {
            $this->_elements = array_values($array);
        }
        
        $this->_count = count($this->_elements);
    }
    
    /**
     * Constructor
     * @param integer $page
     * @param integer $nbByPage
     * @param array $filters 
     */
    public function __construct($page = 1, $nbByPage = 25, array $elements = array()) {
        $this->_page = $page;
        $this->_nbByPage = $nbByPage;
        $this->set($elements);
    }
    
    /**
     * Returns the sql code for limit / offset
     * @return string
     */
    public function getLimit() {
        if (!$this->hasLimit()) {
            return '';
        }
        else {
            $_offset = ($this->_page - 1) * $this->_nbByPage;
            $_sql = " LIMIT $this->_nbByPage OFFSET $_offset ";
            return $_sql;
        }
    }
    
    protected function hasLimit() {
        return ($this->_page >= 0 || $this->_nbByPage >= 0);
    }
    
    public function __toString() {
        return $this->getLimit();
    }
    
    public function getPage() {
        return $this->_page;
    }
    
    public function setPage($pageNumber) {
        $this->_page = $pageNumber;
    }
    
    public function getNbByPage() {
        return $this->_nbByPage;
    }
    
    public function setNbByPage($nbByPage) {
        $this->_nbByPage = $nbByPage;
    }

    public function count() {
        return $this->_count;
    }
    
    public function clear() {
        $this->set(array());
    }
    
    public function getResultSet() {
        if (reset($this->_elements) instanceof Evaneos_Berthe_AbstractVO) {
            $res = array();
            foreach($this->_elements as $key => /* @var $value Evaneos_Berthe_AbstractVO */ $value) {
                $res[$value->id] = $value;
            }
            return $res;
        }
        else {
            return $this->_elements;
        }
    }
            
    public function getTtlCount() {
        return $this->_ttlCount;
    }
    
    public function setTtlCount($ttlCount) {
        if($ttlCount >= $this->count()) {
            $this->_ttlCount = (int)$ttlCount;
        } else {
            $this->_ttlCount = $this->count();
        }
    }
    
    public function getNbPages() {
        return ceil((float)$this->_ttlCount / (float)$this->_nbByPage);
    }
}