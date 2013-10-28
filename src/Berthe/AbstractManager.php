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
     * @var Berthe_AbstractStorage
     */
    protected $_storage = null;

    /**
     * @var Berthe_AbstractValidator
     */
    protected $_validator = null;

    /**
     * @var Berthe_FactoryManager
     */
    protected $_managerFactory = null;

    protected $validateHooks = array();
    protected $saveHooks = array();
    protected $deleteHooks = array();

    /**
     * @return Berthe_AbstractValidator
     */
    abstract protected function _getValidator();

    /**
     * @return Berthe_AbstractStorage
     */
    abstract protected function _getStorage();

    /**
     * Return a new VO with default values
     * @return Berthe_AbstractVO the VO with its default values
     */
    abstract public function getVoForCreation();

    public function __construct() {
        $this->saveHooks[] = new Berthe_Util_DateHandlingSaveHook();
    }

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
        $ret = $this->validate($object);
        if ($ret) {
            $ret = $this->_save($object);
        }
        return $ret;
    }

    final protected function validate($object) {
        foreach($this->validateHooks as /* @var $hook Berthe_AbstractHook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->_getValidator()->validate($object);

        foreach($this->validateHooks as /* @var $hook Berthe_AbstractHook */ $hook) {
            $hook->after($object);
        }

        return $ret;
    }

    final protected function _save($object) {
        foreach($this->saveHooks as /* @var $hook Berthe_AbstractHook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->_getStorage()->save($object);

        foreach($this->saveHooks as /* @var $hook Berthe_AbstractHook */ $hook) {
            $hook->after($object);
        }

        return $ret;
    }

    /**
     * Default method to delete an object
     * @param int $id
     * @return boolean
     */
    final public function delete($object) {
        foreach($this->deleteHooks as /* @var $hook Berthe_AbstractHook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->_getStorage()->delete($object);

        foreach($this->saveHooks as /* @var $hook Berthe_AbstractHook */ $hook) {
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