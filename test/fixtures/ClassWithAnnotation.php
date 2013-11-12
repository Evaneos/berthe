<?php
/**
 * @ACL *
 * @Generic stuff
 */
class ClassWithAnnotation {
    public function __construct() {

    }

    /**
     * @NoTransaction
     */
    public function methodNoTransaction() {

    }

    /**
     * @ACL admin
     */
    public function methodDifferentACL() {

    }
}