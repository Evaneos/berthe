<?php
namespace Berthe\Validation;

use Berthe\ as Berthe;


abstract class AbstractValidator implements Validator {
    /**
     * @var Berthe\ErrorHandler\Errors
     */
    protected $errors = null;
    /**
     * @var AbstractHook[]
     */
    protected $validateHooks = array();

    public function setErrors(Berthe\ErrorHandler\Errors $errors) {
        $this->errors = $errors;
        return $this;
    }

    /**
     * @return Berthe\ErrorHandler\Errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * @param Berthe\AbstractHook $hook
     * @param string $name
     * @return Validator
     */
    public function addHook(Berthe\AbstractHook $hook, $name) {
        if(!is_scalar($name)) {
            throw new \RuntimeException('Cannot add hook, hookname not scalar');
        }

        if ($this->hasHook($name)) {
            throw new \RuntimeException('Cannot add hook, a hook with that name is already registered');
        }

        $this->validateHooks[$name] = $hook;
        return $this;
    }

    /**
     * @param string $name
     * @return Validator
     */
    public function delHook($name) {
        if (!$this->hasHook($name)) {
            throw new \RuntimeException('Cannot remove hook, no hook exists with that name');
        }

        unset($this->validateHooks[$name]);
        return $this;
    }

    public function hasHook($name) {
        return array_key_exists($name, $this->validateHooks);
    }

    final public function validateSave($object) {
        foreach($this->validateHooks as /* @var $hook Berthe\AbstractHook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->doValidateSave($object);

        foreach($this->validateHooks as /* @var $hook Berthe\AbstractHook */ $hook) {
            $hook->after($object);
        }

        return $ret;
    }

    final public function validateDelete($object) {
        foreach($this->validateHooks as /* @var $hook Berthe\AbstractHook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->doValidateDelete($object);

        foreach($this->validateHooks as /* @var $hook Berthe\AbstractHook */ $hook) {
            $hook->after($object);
        }

        return $ret;
    }

    abstract protected function doValidateSave(Berthe\AbstractVO $vo);
    abstract protected function doValidateDelete(Berthe\AbstractVO $vo);
}