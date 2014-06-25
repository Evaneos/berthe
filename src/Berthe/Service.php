<?php
namespace Berthe;

use Berthe\Fetcher\Fetchable;

interface Service extends Fetchable {

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
     * Creates a new VO
     * @param array $data
     * @return VO
     */
    public function createNew(array $data = array());
    
    /**
     * Default method to save (insert or update depending on context) an object
     * @param VO    $object
     * @param array $data
     * @return VO
     */
    public function save($object, $data = null);

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