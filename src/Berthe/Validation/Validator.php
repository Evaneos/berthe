<?php

namespace Berthe\Validation;
use Berthe\ as Berthe;

interface Validator {
    /**
     * @return ErrorHandler\Errors
     */
    function getErrors();
    function setErrors(Berthe\ErrorHandler\Errors $errors);

    function validateSave($object);
    function validateDelete($object);

    function addHook(Berthe\AbstractHook $hook, $name);
    function delHook($name);
    function hasHook($name);
}