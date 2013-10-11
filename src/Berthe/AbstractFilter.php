<?php

/**
 * Class definition for Berthe abstract Manager Evaneos_Berthe_AbstractFilter
 * 
 * @author anthony@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/Manager.php
 * @package Evaneos/Berthe
 * @since Berthe
 */
abstract class Evaneos_Berthe_AbstractFilter implements ArrayAccess {
    /**
     * Filters set
     * @var array
     */
    protected $_filters = array();
    protected $_sort = array();
    protected $_like = '';
    
    /**
     * Checks if an offset exists
     * @param string $offset
     * @return boolean 
     */
    public function offsetExists($offset) {
        return isset($this->_filters[$offset]);
    }
    
    /**
     * Gets an offset
     * @param type $offset
     * @return type 
     */
    public function offsetGet($offset) {
        return is_string($offset) ? $this->_filters[$offset] : null;
    }
    
    /**
     * sets an offset
     * @param type $offset
     * @param type $value 
     */
    public function offsetSet($offset, $value) {
        !is_string($offset) and trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Only accepts string offsets', E_USER_WARNING);
        is_string($offset) and $this->_filters[$offset] = $value;
    }
    
    /**
     * Unsets an offset
     * @param type $offset 
     */
    public function offsetUnset($offset) {
        !is_string($offset) and trigger_error(__CLASS__ . '::' . __FUNCTION__ . '() : Only accepts string offsets', E_USER_WARNING);
        if(is_string($offset)) {
            unset($this->_filters[$offset]);
        }
    }
    
    /**
     * Constructor
     * @param array $filters 
     * @param array $sort must be an arrays of arrays with deux values : field and order
     *          ie : array(
     *                  array('field1', 'ASC'),
     *                  array('field2', 'DESC'),
     *                  ...
     *               )
     */
    public function __construct(array $filters = array(), array $sort = array(), $like = false) {
        $this->set($filters);
        $this->setSort($sort);
        if(is_string($like)) {
            $this->_like = $like;
        }
    }
    
    /**
     * Sets filters directly from an array
     * @param array $filters 
     */
    public function set(array $filters = array()) {
        $this->_initFilters();
        foreach (array_keys($filters) as $key) {
            $this->offsetSet($key, $filters[$key]);
        }
    }
    
    public function setSort(array $sort = array()) {
        $this->_sort = array();
        foreach ($sort as $value) {
            $_res = false;
            if(is_array($value)) {
                if(count($value) == 2 && is_string($value[0]) && is_string($value[1])) {
                    switch($value[1]) {
                        case 'ASC' :
                        case 'DESC' :
                            $this->_sort[] = array($value[0], $value[1]);
                            $_res = true;
                    }
                } else {
                    $this->_sort[] = array($value[0], 'ASC');
                    $_res = true;
                }
            }
            if(!$_res) {
                throw new Exception('Invalid entry for sort');
            }
        }
    }
    
    /**
     * Inits the filters
     */
    private function _initFilters() {
        $this->_filters = array();
    }
    
    /**
     * String convertion function
     * @return string 
     */
    public function __toString() {
        return $this->getConditions();
    }
    
    /**
     * Returns the conditions as string to be inserted in a query
     * @return string
     */
    abstract public function getConditions();
    
    /**
     * Returns the sorting as string to be inserted in a query
     * @return string
     */
    abstract public function getSort();
}