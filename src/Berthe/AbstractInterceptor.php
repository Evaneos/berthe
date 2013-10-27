<?php
abstract class Berthe_AbstractInterceptor {
    protected $decorated = null;

    public function __construct() {}

    final public function setDecorated($class) {
        $this->decorated = $class;
        return $this;
    }

    final public function __call($method, $args) {
        return $this->intercept($method, $args);
    }

    protected function invoke($method, $args) {
        return call_user_func_array(array($this->decorated, $method), $args);
    }

    abstract protected function intercept($method, $args);
}