<?php
/**
 * Class definition for Berthe abstract Manager Berthe_AbstractValidator
 *
 * @author dev@evaneos.com
 * @copyright Evaneos
 * @version 1.0
 * @filesource Berthe/Validator.php
 * @package Berthe
 */
interface Berthe_Validator {
    /**
     * Validate the object before insert/update into DAO
     * @return boolean
     */
    public function validate($object);
    /**
     * returns the validator error handler
     * @return Berthe_ErrorHandler
     */
    public function getErrors();
}