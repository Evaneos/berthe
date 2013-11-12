<?php
class MethodAnnotationTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        require_once dirname(dirname(__DIR__)) . '/fixtures/ClassWithAnnotation.php';
    }

    public function tearDown() {

    }


    public function testGetListOfClassAnnotations() {
        $class = new ClassWithAnnotation();
        $annotationReader = new AnnotationReader();
        $direct = true;
        $annotations = $annotationReader->getClassAnnotation($class, $direct);
    }

    public function testGetListOfMethodAnnotations() {
        $class = new ClassWithAnnotation();
        $annotationReader = new AnnotationReader();
        $direct = true;
        $annotations = $annotationReader->getMethodAnnotation($method, $direct);
    }
}