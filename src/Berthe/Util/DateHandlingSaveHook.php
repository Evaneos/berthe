<?php
class Berthe_Util_DateHandlingSaveHook extends Berthe_AbstractHook {
    public function before(Berthe_AbstractVO $object) {
        if (property_exists($object, "created_at") && !$object->id) {
            $object->created_at = new DateTime();
        }

        if (property_exists($object, "createdAt") && !$object->id) {
            $object->created_at = new DateTime();
        }

        if (property_exists($object, "updated_at")) {
            $object->updated_at = new DateTime();
        }

        if (property_exists($object, "updatedAt")) {
            $object->updated_at = new DateTime();
        }
    }

    public function after(Berthe_AbstractVO $object) {

    }
}