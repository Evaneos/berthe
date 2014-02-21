<?php
namespace Berthe;

interface VO {

    /**
     * Set the VO version
     * @param integer $version
     */
    public function setVersion($version);

    /**
     * Get the VO version
     * @return integer
     */
    public function getVersion();

    /**
     * Set the VO id
     * @param mixed $id
     */
    public function setId($id);

    /**
     * Get the VO id
     * @return mixed
     */
    public function getId();

    /**
     * Return an array representation of the VO
     * @return array
     */
    public function __toArray();
}