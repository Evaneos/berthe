<?php

namespace Berthe\DAL;

use Berthe\VO;

abstract class AbstractStore implements Store {
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
     * @param VO $vo
     * @return boolean success
     */
    abstract protected function _insert(VO $vo);
    /**
     * @param VO $vo
     * @return boolean success
     */
    abstract protected function _update(VO $vo);
    /**
     * @param VO $vo
     * @return boolean success
     */
    abstract protected function _delete(VO $vo);

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
     * @param VO $vo
     * @return boolean
     */
    final public function delete(VO $vo) {
        return $this->_delete($vo);
    }

    /**
     * @param VO $vo
     * @return boolean success
     */
    final public function save(VO $vo) {
        $ret = null;
        if ($vo->getId()) {
            $ret = $this->_update($vo);
        }
        else {
            $ret = $this->_insert($vo);
        }

        if ($ret !== false) {
            $ret = true;
        }

        return $ret;
    }

    /**
     * Save multiple objects, returns true if all are saved, false if at least one failed
     * @param VO[] $vos
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