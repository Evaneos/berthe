<?php
/**
 * @Annotation
 * @Target({"CLASS","METHOD", "PROPERTY"})
 */
class Berthe_Annotation_ACL {
    public $role = null;

    public function __construct(array $values = array()) {
        $this->data = $values;

        if (array_key_exists('role', $values)) {
            $this->role = $values['role'];
        }
    }
}