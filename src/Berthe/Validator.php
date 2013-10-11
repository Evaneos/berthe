<?php
/**
 * Class definition for Berthe abstract Manager Evaneos_Berthe_AbstractValidator
 * 
 * @author anthony@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/Validator.php
 * @package Evaneos/Berthe
 * @since Berthe
 */
interface Evaneos_Berthe_Validator {
    /**
     * Validate the object before insert/update into DAO
     * @return boolean 
     */
    public function validate($object);
    /**
     * returns the validator error handler
     * @return Evaneos_Berthe_ErrorHandler
     */
    public function getErrors();
}