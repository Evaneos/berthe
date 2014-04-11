<?php

namespace Berthe\DAL;

interface Writer {
    /**
     * @param Translator $translator
     */
    function setTranslator(Translator $translator);
    /**
     * Insert the object in database
     * @param \Berthe\VO $object the object to insert
     * @return boolean
     */
    public function insert(\Berthe\VO $object);

    /**
     * Update the object in database
     * @param \Berthe\VO $object the object to insert
     * @return boolean
     */
    public function update(\Berthe\VO $object);

    /**
     * Delete the object from database
     * @param \Berthe\VO $object the object to insert
     * @return boolean
     */
    public function delete(\Berthe\VO $object);

    /**
     * Delete an object by id from database
     * @param int $int object identifier
     * @return boolean
     */
    public function deleteById($id);

}