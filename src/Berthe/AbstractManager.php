<?php

namespace Berthe;

abstract class AbstractManager {
    /**
     * @var DAL\AbstractStorage
     */
    protected $storage = null;

    /**
     * @var Validation\Validator
     */
    protected $validator = null;

    protected $saveHooks = array();
    protected $deleteHooks = array();

    /**
     * @return Validation\Validator
     */
    public function getValidator() {
        return $this->validator;
    }

    /**
     * @param Validation\Validator $validator
     * @return AbstractManager
     */
    public function setValidator(Validation\Validator $validator) {
        $this->validator = $validator;
        return $this;
    }

    public function getStorage() {
        return $this->storage;
    }

    public function setStorage(DAL\AbstractStorage $storage) {
        $this->storage = $storage;
        return $this->storage;
    }

    /**
     * Return a new VO with default values
     * @return Berthe\AbstractVO the VO with its default values
     */
    abstract public function getVoForCreation();

    public function __construct() {

    }

    /**
     * @return Berthe\AbstractVO[]
     */
    public function getAll() {
        $pagi = new Fetcher(-1, -1);
        $pagi->addSort('id', Fetcher::SORT_ASC);
        return $this->getByPaginator($pagi)->getResultSet();
    }

    /**
     * Default method to get an object by its id
     * @param int $id
     * @return Berthe\AbstractVO
     */
    public function getById($id) {
        $_ret = $this->getStorage()->getById($id);
        return $_ret;
    }

    /**
     * Default method to get a list of objects with a list of ids
     * @param array $ids
     * @return Berthe\AbstractVO
     */
    public function getByIds(array $ids = array()) {
        return $this->getStorage()->getByIds($ids);
    }

    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function getByPaginator(Fetcher $paginator) {
        $paginator = $this->getStorage()->getByPaginator($paginator);
        return $paginator;
    }

    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher
     */
    public function getColumnByPaginator(Fetcher $paginator, $columnName = "id") {
        $paginator = $this->getStorage()->getColumnByPaginator($paginator, $columnName);
        return $paginator;
    }

    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher
     */
    public function getColumnByPaginatorPreserveIds(Fetcher $paginator, $columnName = "id") {
        $paginator = $this->getStorage()->getColumnByPaginatorPreserveIds($paginator, $columnName);
        return $paginator;
    }

    /**
     * @param Fetcher $paginator
     * @return string sql
     */
    public function getSqlByPaginator($paginator) {
        return $this->getStorage()->getSqlByPaginator($paginator);
    }

    /**
     * @param \Berthe\AbstractVO $vo
     * @param Fetcher $paginator
     * @param int $nbBefore
     * @param int $nbAfter
     * @return array array[voBefore[], voAfter[]]  BEFORE / AFTER
     */
    public function getNextAndPreviousByPaginator(\Berthe\AbstractVO $vo, Fetcher $paginator, $nbBefore = 1, $nbAfter = 1) {
        return $this->getStorage()->getNextAndPreviousByPaginator($vo, $paginator, $nbBefore, $nbAfter);
    }

    /**
     * Default method to save (insert or update depending on context) an object
     * @param Berthe\AbstractVO $object
     * @return boolean
     */
    public function save($object) {
        $ret = $this->getValidator()->validateSave($object);
        if ($ret) {
            $ret = $this->_save($object);
        }
        return $ret;
    }

    protected function _save($object) {
        foreach($this->saveHooks as /* @var $hook Berthe\AbstractHook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->getStorage()->save($object);

        foreach($this->saveHooks as /* @var $hook Berthe\AbstractHook */ $hook) {
            $hook->after($object);
        }

        return $ret;
    }

    /**
     * Default method to delete an object
     * @param int $id
     * @return boolean
     */
    public function delete($object) {
        $ret = $this->getValidator()->validateDelete($object);
        if ($ret) {
            $ret = $this->_delete($object);
        }
        return $ret;
    }

    protected function _delete($object) {
        foreach($this->deleteHooks as /* @var $hook Berthe\AbstractHook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->getStorage()->delete($object);

        foreach($this->saveHooks as /* @var $hook Berthe\AbstractHook */ $hook) {
            $hook->after($object);
        }

        return $ret;
    }

    /**
     * Default method to delete an object by its id
     * @param int $id
     * @return boolean
     */
    public function deleteById($id) {
        $object = $this->getById($id);
        return $this->delete($object);
    }

    /**
     * Getter and setter to ignore cache flag
     * @param boolean|null $shallIgnore
     */
    public function ignoreAllCache($shallIgnore = null){
        $this->_getStorage()->ignoreAllCache($shallIgnore);
    }
}