<?php

namespace Berthe;

interface Manager {

    /**
     * Get validator
     * @return Validation\Validator
     */
    public function getValidator();

    /**
     * Get storage
     * @return DAL\AbstractStorage
     */
    public function getStorage();

    /**
     * Return a new VO with default values
     * @return Berthe\VO the VO with its default values
     */
    public function getVoForCreation();

    /**
     * @return Berthe\VO[]
     */
    public function getAll();

    /**
     * Default method to get an object by its id
     * @param int $id
     * @return Berthe\VO
     */
    public function getById($id);
    /**
     * Default method to get a list of objects with a list of ids
     * @param array $ids
     * @return Berthe\VO
     */
    public function getByIds(array $ids = array());

    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function getByPaginator(Fetcher $paginator);

    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher
     */
    public function getColumnByPaginator(Fetcher $paginator, $columnName = "id");
    /**
     * @param Fetcher $paginator
     * @param string $columnName
     * @return Fetcher
     */
    public function getColumnByPaginatorPreserveIds(Fetcher $paginator, $columnName = "id");

    /**
     * @param Fetcher $paginator
     * @return string sql
     */
    public function getSqlByPaginator($paginator);
    /**
     * @param \Berthe\VO $vo
     * @param Fetcher $paginator
     * @param int $nbBefore
     * @param int $nbAfter
     * @return array array[voBefore[], voAfter[]]  BEFORE / AFTER
     */
    public function getNextAndPreviousByPaginator(\Berthe\VO $vo, Fetcher $paginator, $nbBefore = 1, $nbAfter = 1);

    /**
     * Default method to save (insert or update depending on context) an object
     * @param Berthe\VO $object
     * @return boolean
     */
    public function save($object);

    /**
     * Default method to delete an object
     * @param int $id
     * @return boolean
     */
    public function delete($object);

    /**
     * Default method to delete an object by its id
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
    
    /**
     * Getter and setter to ignore cache flag
     * @param boolean|null $shallIgnore
     */
    public function ignoreAllCache($shallIgnore = null);
}