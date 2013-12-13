<?php
namespace Berthe\Interceptor;

interface Interceptor {
    function __construct($classToIntercept = null);
    function setDecorated($class);
    function getMainDecorated();
}