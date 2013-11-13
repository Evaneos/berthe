<?php
class Berthe_Interceptor_ACLChecker extends Berthe_AbstractInterceptor {
    protected function intercept($method, $args) {
        $reader = new Doctrine\Common\Annotations\AnnotationReader();

        $reflectClass = new ReflectionClass($this->getMainDecorated());
        $reflectMethod = $reflectClass->getMethod($method);

        $aclAnnotation = $reader->getMethodAnnotation($reflectMethod, 'Berthe_Annotation_ACL');
        if ($aclAnnotation) {
            if ($aclAnnotation->role === $_SESSION['role']) {
                return $this->invoke($method, $args);
            }
            else {
                throw new LogicException('Not Allowed !');
            }
        }
        else {
            return $this->invoke($method, $args);
        }
    }
}