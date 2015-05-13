<?php

namespace Berthe\Validation;

use Berthe as Berthe;

interface Validator
{
    /**
     * @return ErrorHandler\FunctionalErrorListException
     */
    public function getErrors();
    public function setErrors(\Berthe\ErrorHandler\FunctionalErrorListException $errors);

    public function validateSave($object);
    public function validateDelete($object);

    public function addHook(Berthe\Hook $hook, $name);
    public function delHook($name);
    public function hasHook($name);
}
