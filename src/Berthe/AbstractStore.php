<?php
abstract class Berthe_AbstractStore {
    /**
     * @var boolean 
     */
    protected $isEnabled = true;
    /**
     * @var boolean
     */
    protected $isPersistent = false;
    /**
     * @var boolean
     */
    protected $isTTLAble = false;
    
    /**
     * @param array $ids
     * @return array id=>object
     */
    abstract protected function _load(array $ids = array());
    /**
     * @param Berthe_AbstractVO $vo
     * @return boolean success
     */
    abstract protected function _insert(Berthe_AbstractVO &$vo);
    /**
     * @param Berthe_AbstractVO $vo
     * @return boolean success
     */
    abstract protected function _update(Berthe_AbstractVO &$vo);
    /**
     * @param Berthe_AbstractVO $vo
     * @return boolean success
     */
    abstract protected function _delete(Berthe_AbstractVO &$vo);
    
    /**
     * Getter and setter for isEnabled toggle
     * @param mixed $bool bool|null
     * @return boolean
     */
    public function isEnabled($bool = null) {
        if ($bool === true || $bool === false) {
            $this->isEnabled = $bool;
        }
        
        return $this->isEnabled;
    }
    /**
     * @return boolean
     */
    public function isPersistent() {
        return $this->isPersistent;
    }
    /**
     * @return boolean
     */
    public function isTTLAble() {
        return $this->isTTLAble;
    }
    
    /**
     * @param array $ids
     * @return array id=>object
     */
    final public function load(array $ids = array()) {
        if($this->isEnabled()) {
            return $this->_load($ids);
        }
        else {
            return array();
        }
    }
    
    /**
     * @param Berthe_AbstractVO $vo
     * @return boolean
     */
    final public function delete(Berthe_AbstractVO &$vo) {
        return $this->_delete($vo);
    }
    
    /**
     * @param Berthe_AbstractVO $vo
     * @return boolean success
     */
    final public function save(Berthe_AbstractVO &$vo) {
        $ret = null;
        if ($vo->id) {
            $ret = $this->_update($vo);
        }
        else {
            $ret = $this->_insert($vo);
        }
        
        // @TOREFACTOR O.M. This check will help us find where someone forgot to add a return value in update/insert
        if (!is_bool($ret)) {
            trigger_error("SAVE method doesn't return a boolean value for class " . get_class($vo), E_USER_NOTICE);
        }
        
        // @TOREFACTOR O.M. I put that because some morons don't return a value in INSERT and UPDATE of their DAOWriter
        if ($ret !== false) {
            $ret = true;
        }
        
        return $ret;
    }
    
    /**
     * Save multiple objects, returns true if all are saved, false if at least one failed
     * @param Berthe_AbstractVO[] $vos
     * @return boolean false if at least one save has failed
     */
    final public function saveMulti(array $vos = array()) {
        return $this->_saveMulti($vos);
    }
    
    /**
     * 
     * @param array $vos
     * @return boolean
     */
    protected function _saveMulti(array $vos = array()) {
        $isOk = true;
        foreach($vos as $vo) {
            $ret = $this->save($vo);
            if (!$ret) {
                $isOk = false;
            }
        }
        return $isOk;
    }
}