<?php
use Doctrine\Common\Annotations\AnnotationRegistry;

AnnotationRegistry::registerLoader(function($class) {
    $file = str_replace("\\", DIRECTORY_SEPARATOR, $class) . ".php";

    $filepath = __DIR__ . '/Annotations/' . $file;

    if (file_exists($filepath)) {
        // file exists makes sure that the loader fails silently
        require $filepath;
    }

    return false;
});