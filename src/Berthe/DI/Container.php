<?php
class Berthe_DI_Container {
    protected $config = null;
    protected $provide = array();

    public function __construct($config) {
        $this->config = $config;
        $this->loadYml($config);
    }

    protected function loadYml($config) {
        $yml = array();
        $dirname = dirname($config);
        $yaml = new Symfony\Component\Yaml\Yaml();
        $res = $yaml->parse($config);
        if (array_key_exists('include', $res)) {
            foreach($res['include'] as $key => $value) {
                $yml = array_merge_recursive($yml, $yaml->parse($dirname . '/'. $value));
            }
        }

        $this->provide = $yml;
    }

    public function getParameter($parameterName) {
        $toReturn = null;
        if (array_key_exists('parameters', $this->provide)) {
            if (array_key_exists($parameterName, $this->provide['parameters'])) {
                $toReturn = $this->provide['parameters'][$parameterName];
            }
        }

        return $toReturn;
    }

    public function get($serviceName) {
        if (count($this->provide) > 0) {
            if (array_key_exists('classes', $this->provide) && array_key_exists($serviceName, $this->provide['classes'])) {
                return $this->loadService($this->provide['classes'][$serviceName]);
            }
            else {
                throw new RuntimeException('Class not configured');
            }
        }
        else {
            throw new RuntimeException('Container not loaded');
        }
    }

    protected function loadService($serviceConfig) {
        // if (array_key_exists('interceptor', $serviceName)) {
        //     if (is_array($serviceName)) {
        //         foreach($serviceName['interceptor'] as $interceptorName) {
        //             $interceptor = $this->get($interceptorName);
        //         }
        //     }
        // }
        $class = $this->classInstanciation($serviceConfig);
        $this->classProps($class, $serviceConfig);
        $this->classCalls($class, $serviceConfig);
        $classEncapsulated = $this->classInterceptor($class, $serviceConfig);
        return $classEncapsulated;
    }

    protected function classInstanciation($serviceConfig) {
        $className = null;
        $constructorArgs = array();

        if (array_key_exists('class', $serviceConfig)) {
            $className = $serviceConfig['class'];
        }
        else {
            throw new RuntimeException('no class name defined for service' . serialize($serviceName));
        }

        if (array_key_exists('arguments', $serviceConfig)) {
            $constructorArgs = $serviceConfig['arguments'];
        }

        try {
            $instanciated = null;
            $class = new ReflectionClass($className);
            if (count($constructorArgs) > 0) {
                $instanciated = $class->newInstanceArgs($constructorArgs);
            }
            else {
                $instanciated = $class->newInstance();
            }
            return $instanciated;
        }
        catch(Exception $e) {
            throw new RuntimeException('Couldn\'t instanciate class ' . $className, 0, $e);
        }
    }

    protected function classCalls($class, $serviceConfig) {
        $callConfig = array();
        if (array_key_exists('call', $serviceConfig)) {
            $callConfig = $serviceConfig['call'];
        }
        foreach($callConfig as $methodName => $parameters) {
            $convertedParameters = $this->convertParameters($parameters);
            call_user_func_array(array($class, $methodName), $convertedParameters);
        }

        return true;
    }

    protected function classProps($class, $serviceConfig) {
        $propConfig = array();
        if (array_key_exists('props', $serviceConfig)) {
            $propConfig = $serviceConfig['props'];
        }
        foreach($propConfig as $propName => $propValue) {
            $convertedValue = $this->convertValue($propValue);
            $class->$propName = $convertedValue;
        }

        return true;
    }

    protected function convertValue($value) {
        $toReturn = null;
        $prefix = substr($value, 0, 1);
        switch($prefix) {
            case '@' :
                $toReturn = $this->get(substr($value, 1));
                break;
            case '%' :
                $toReturn = $this->getParameter(substr($value, 1));
                break;
            default :
                $toReturn = $value;
                break;
        }
        return $toReturn;
    }

    protected function convertParameters($parameters) {
        $convertedParameters = array();
        foreach($parameters as $value) {
            $convertedValue = $this->convertValue($value);
            $convertedParameters[] = $convertedValue;
        }
        return $convertedParameters;
    }

    /**
     * @todo  inject interceptor around the class
     */
    protected function classInterceptor($class, $serviceConfig) {
        return $class;
    }
}