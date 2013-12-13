<?php
namespace Berthe\Util;

use Berthe as Berthe;

class DateHandlingSaveHook extends Berthe\AbstractHook {
    public function before($data) {
        if (!is_object($data)) {
            throw new \RuntimeException('Not an object');
        }

        if (property_exists($data, "created_at") && !$data->id) {
            $object->created_at = new \DateTime();
        }

        if (property_exists($data, "createdAt") && !$data->id) {
            $data->created_at = new \DateTime();
        }

        if (property_exists($data, "updated_at")) {
            $data->updated_at = new \DateTime();
        }

        if (property_exists($data, "updatedAt")) {
            $data->updated_at = new \DateTime();
        }
    }

    public function after($data) {

    }
}