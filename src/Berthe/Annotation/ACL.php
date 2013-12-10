<?php
namespace Evaneos\Berthe\Annotation;

use Doctrine\Common\Annotations as Doctrine;

/**
 * @Annotation
 * @Target({"CLASS","METHOD", "PROPERTY"})
 */
class ACL extends Doctrine\Annotation {
    public $role = null;

    public function __construct(array $values = array()) {
        $this->data = $values;

        if (array_key_exists('role', $values)) {
            $this->role = $values['role'];
        }
    }
}