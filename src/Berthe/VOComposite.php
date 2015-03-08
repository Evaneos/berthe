<?php

namespace Berthe;

class VOComposite
{
    /**
     * Internal component
     * @var object
     */
    private $component;

    /**
     * @param $component
     */
    public function __construct($component)
    {
        $this->component = $component;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return self|mixed
     */
    public function __call($name, $arguments)
    {
        if (!method_exists($this->component, $name)) {
            throw new \BadMethodCallException(sprintf('%s::%s doesn\'t exist', get_class($this->component), $name));
        }
        $ret = call_user_func_array(array($this->component, $name), $arguments);

        // Don't break setter chaining
        if ($ret === $this->component) {
            return $this;
        }

        return $ret;
    }

    /**
     * @return object
     */
    protected function getComponent()
    {
        return $this->component;
    }
}
