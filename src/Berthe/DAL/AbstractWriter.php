<?php

namespace Berthe\DAL;

abstract class AbstractWriter {
    /**
     * @var DbWriter
     */
    protected $db = null;

    public function setDb(DbWriter $db) {
        $this->db = $db;
        return $this;
    }

    /**
     * Insert the object in database
     * @param \Berthe\AbstractVO $object the object to insert
     * @return boolean
     */
    abstract public function insert(\Berthe\AbstractVO $object);
    /**
     * Update the object in database
     * @param \Berthe\AbstractVO $object the object to insert
     * @return boolean
     */
    abstract public function update(\Berthe\AbstractVO $object);
    /**
     * Delete the object from database
     * @param \Berthe\AbstractVO $object the object to insert
     * @return boolean
     */
    abstract public function delete(\Berthe\AbstractVO $object);
    /**
     * Delete an object by id from database
     * @param int $int object identifier
     * @return boolean
     */
    abstract public function deleteById($id);
}