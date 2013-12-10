<?php
namespace Evaneos\Berthe\Interceptor;

interface Interceptor {
    function __construct($classToIntercept);
    function setDecorated($class);
    function getMainDecorated();
}