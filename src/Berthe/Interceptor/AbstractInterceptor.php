<?php
namespace Berthe\Interceptor;

abstract class AbstractInterceptor implements Interceptor
{
    protected $decorated = null;
    protected $mainDecorated = null;

    public function __construct($classToIntercept = null)
    {
        $this->setDecorated($classToIntercept);
    }

    final public function setDecorated($class)
    {
        $this->decorated = $class;

        if ($class instanceof AbstractInterceptor) {
            $this->mainDecorated = $class->mainDecorated;
        } else {
            $this->mainDecorated = $class;
        }

        return $this;
    }

    final public function getMainDecorated()
    {
        return $this->mainDecorated;
    }

    final public function __call($method, $args)
    {
        return $this->intercept($method, $args);
    }

    protected function invoke($method, $args)
    {
        return call_user_func_array(array($this->decorated, $method), $args);
    }

    abstract protected function intercept($method, $args);
}
