<?php

namespace Berthe\DAL;

use Berthe\VO;

class StoreArray extends AbstractStore
{
    protected $objects = array();

    protected function _load(array $ids = array())
    {
        $output = array();
        foreach ($ids as $id) {
            if (array_key_exists($id, $this->objects)) {
                $output[(int)$id] = $this->objects[$id];
            }
        }
        return $output;
    }

    protected function _saveMulti(array $vos = array())
    {
        $this->objects = $vos + $this->objects;
        return true;
    }

    protected function _insert(VO $vo)
    {
        if ($vo->getId()) {
            $this->objects[(int)$vo->getId()] = $vo;
        }
        return true;
    }

    protected function _update(VO $vo)
    {
        if ($vo->getId()) {
            $this->objects[(int)$vo->getId()] = $vo;
        }
        return true;
    }

    protected function _delete(VO $vo)
    {
        if ($vo->getId()) {
            $id = (int) $vo->getId();
            if (array_key_exists($id, $this->objects)) {
                unset($this->objects[$id]);
            }
        }
        return true;
    }
}
