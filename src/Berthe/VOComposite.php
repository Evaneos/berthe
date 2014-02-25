<?php

namespace Berthe;

class VOComposite
{
    /**
     * Internal component
     * @var \Berthe\VO
     */
    private $component;

    public function __construct(VO $component)
    {
        $this->component = $component;
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->component, $name)) {
           return call_user_func_array(array($this->component, $name), $arguments);
        }
    }
}