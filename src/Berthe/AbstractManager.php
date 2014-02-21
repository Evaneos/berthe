<?php

namespace Berthe;

abstract class AbstractManager implements Manager {

    /**
     * VO class name
     * @var string
     */
    protected $VOClass = null;

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
     * @return VO the VO with its default values
     */
    public function getVoForCreation() {
        if($this->VOClass) {
            return new $this->VOClass;
        }

        throw new \RuntimeException('VOClass is not defined for ' . get_called_class());
    }

    /**
     * @return VO[]
     */
    public function getAll() {
        $pagi = new Fetcher(-1, -1);
        $pagi->addSort('id', Fetcher::SORT_ASC);
        return $this->getByPaginator($pagi)->getResultSet();
    }

    /**
     * Default method to get an object by its id
     * @param int $id
     * @return VO
     */
    public function getById($id) {
        return $this->getStorage()->getById($id);
    }

    /**
     * Default method to get a list of objects with a list of ids
     * @param array $ids
     * @return VO[]
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
     * @param VO $vo
     * @param Fetcher $paginator
     * @param int $nbBefore
     * @param int $nbAfter
     * @return [VO[], VO[]]  BEFORE / AFTER
     */
    public function getNextAndPreviousByPaginator(VO $vo, Fetcher $paginator, $nbBefore = 1, $nbAfter = 1) {
        return $this->getStorage()->getNextAndPreviousByPaginator($vo, $paginator, $nbBefore, $nbAfter);
    }

    /**
     * Default method to save (insert or update depending on context) an object
     * @param VO $object
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
        foreach($this->saveHooks as /* @var $hook Hook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->getStorage()->save($object);

        foreach($this->saveHooks as /* @var $hook Hook */ $hook) {
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
        foreach($this->deleteHooks as /* @var $hook Hook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->getStorage()->delete($object);

        foreach($this->saveHooks as /* @var $hook Hook */ $hook) {
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