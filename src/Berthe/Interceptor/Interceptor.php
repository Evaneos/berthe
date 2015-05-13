<?php
namespace Berthe\Interceptor;

interface Interceptor
{
    public function __construct($classToIntercept = null);
    public function setDecorated($class);
    public function getMainDecorated();
}
