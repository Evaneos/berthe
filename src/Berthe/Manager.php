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
     * @return DAL\Storage
     */
    public function getStorage();

    /**
     * Return a new VO with default values
     * @return VO the VO with its default values
     */
    public function getVoForCreation();

    /**
     * @return VO[]
     */
    public function getAll();

    /**
     * Default method to get an object by its id
     * @param int $id
     * @return VO
     */
    public function getById($id);
    /**
     * Default method to get a list of objects with a list of ids
     * @param array $ids
     * @return VO[]
     */
    public function getByIds(array $ids = array());

    /**
     * @param Fetcher $paginator
     * @return Fetcher
     */
    public function getByFetcher(Fetcher $fetcher);
    /**
     * Default method to save (insert or update depending on context) an object
     * @param VO $object
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
}