<?php

namespace Berthe;

abstract class AbstractManager implements Manager
{

    /**
     * VO class name
     * @var string
     */
    protected $VOFQCN = null;

    /**
     * @var DAL\AbstractStorage
     */
    protected $storage = null;

    /**
     * @var Validation\Validator
     */
    protected $validator = null;

    /**
     * @var Hook[]
     */
    protected $saveHooks = array();

    /**
     * @var Hook[]
     */
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

    /**
     * @return DAL\AbstractStorage $storage
     */
    public function getStorage() {
        return $this->storage;
    }

    /**
     * @param DAL\AbstractStorage $storage
     * @return self
     */
    public function setStorage(DAL\AbstractStorage $storage) {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @param Hook $hook
     */
    public function addSaveHook(Hook $hook) {
        $this->saveHooks[] = $hook;
    }

    /**
     * @param Hook $hook
     */
    public function addDeleteHook(Hook $hook) {
        $this->deleteHooks[] = $hook;
    }

    /**
     * set VO Fully-Qualified Class Name
     * @param string $VOFQCN
     */
    public function setVOFQCN($VOFQCN) {
        $this->VOFQCN = $VOFQCN;
        return $this;
    }

    /**
     * Return a new VO with default values
     * @return VO the VO with its default values
     */
    public function getVOForCreation() {
        if ($this->VOFQCN) {
            return new $this->VOFQCN;
        }

        throw new \RuntimeException('VOFQCN is not defined for ' . get_called_class());
    }

    /**
     * @return VO[]
     */
    public function getAll() {
        $fetcher = new Fetcher(-1, -1);
        $fetcher->sortById(Fetcher::SORT_ASC);
        return $this->getByFetcher($fetcher)->getResultSet();
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
     * @param Fetcher $fetcher
     * @return int
     */
    public function getCountByFetcher(Fetcher $fetcher) {
        return $this->getStorage()->getCountByFetcher($fetcher);
    }

    /**
     * @param Fetcher $fetcher
     * @return Fetcher
     */
    public function getByFetcher(Fetcher $fetcher) {
        $fetcher = $this->getStorage()->getByFetcher($fetcher);
        return $fetcher;
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
}
