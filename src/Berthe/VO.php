<?php
namespace Berthe;

interface VO {

    /**
     * Provide
     * @return array
     */
    function getTranslatableFields();

    /**
     * Provide
     * @return array
     */
    function getDatetimeFields();

    /**
     * Set the VO version
     * @param integer $version
     */
    function setVersion($version);

    /**
     * Get the VO version
     * @return integer
     */
    function getVersion();

    /**
     * Set the VO id
     * @param mixed $id
     */
    function setId($id);

    /**
     * Get the VO id
     * @return mixed
     */
    function getId();

    /**
     * Return an array representation of the VO
     * @return array
     */
    function __toArray();
}