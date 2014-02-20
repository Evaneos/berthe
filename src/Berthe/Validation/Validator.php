<?php

namespace Berthe\Validation;
use Berthe as Berthe;

interface Validator {
    /**
     * @return ErrorHandler\FunctionalErrorListException
     */
    function getErrors();
    function setErrors(\Berthe\ErrorHandler\FunctionalErrorListException $errors);

    function validateSave($object);
    function validateDelete($object);

    function addHook(Berthe\AbstractHook $hook, $name);
    function delHook($name);
    function hasHook($name);
}