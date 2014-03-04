<?php
namespace Berthe\Validation;

use Berthe;

abstract class AbstractValidator implements Validator {
    /**
     * @var Berthe\ErrorHandler\FunctionalErrorListException
     */
    protected $exception = null;
    /**
     * @var Berthe\Hook[]
     */
    protected $validateHooks = array();

    public function setErrors(Berthe\ErrorHandler\FunctionalErrorListException $exception) {
        $this->exception = $exception;
        return $this;
    }

    /**
     * @return Berthe\ErrorHandler\FunctionalErrorListException
     */
    public function getErrors() {
        return $this->exception;
    }

    /**
     * @param Berthe\Hook $hook
     * @param string $name
     * @return Validator
     */
    public function addHook(Berthe\Hook $hook, $name) {
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
        foreach($this->validateHooks as /* @var $hook Berthe\Hook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->doValidateSave($object);

        foreach($this->validateHooks as /* @var $hook Berthe\Hook */ $hook) {
            $hook->after($object);
        }

        if ($this->getErrors() && $this->getErrors()->hasErrors()) {
            $this->getErrors()->throwMe();
        }

        return $ret;
    }

    final public function validateDelete($object) {
        foreach($this->validateHooks as /* @var $hook Berthe\Hook */ $hook) {
            $hook->before($object);
        }

        $ret = $this->doValidateDelete($object);

        foreach($this->validateHooks as /* @var $hook Berthe\Hook */ $hook) {
            $hook->after($object);
        }

        if ($this->getErrors() && $this->getErrors()->hasErrors()) {
            $this->getErrors()->throwMe();
        }

        return $ret;
    }

    protected function doValidateSave(Berthe\AbstractVO $vo) {
        if ($vo->id) {
            return $this->doValidateUpdate($vo);
        }

        return $this->doValidateCreate($vo);
    }

    protected function doValidateUpdate(Berthe\AbstractVO $vo) {
        return true;
    }

    protected function doValidateCreate(Berthe\AbstractVO $vo) {
        return true;
    }

    protected function doValidateDelete(Berthe\AbstractVO $vo) {
        return true;
    }
}
