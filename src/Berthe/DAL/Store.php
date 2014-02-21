<?php

namespace Berthe\DAL;

interface Store {

    /**
     * Getter and setter for isEnabled toggle
     * @param mixed $bool bool|null
     * @return boolean
     */
    public function isEnabled($bool = null);

    /**
     * @return boolean
     */
    public function isPersistent();

    /**
     * @return boolean
     */
    public function isTTLAble();

    /**
     * @param array $ids
     * @return array id=>object
     */
    public function load(array $ids = array());

    /**
     * @param \Berthe\VO $vo
     * @return boolean
     */
    public function delete(\Berthe\VO &$vo);

    /**
     * @param \Berthe\VO $vo
     * @return boolean success
     */
    public function save(\Berthe\VO &$vo);

    /**
     * Save multiple objects, returns true if all are saved, false if at least one failed
     * @param \Berthe\VO[] $vos
     * @return boolean false if at least one save has failed
     */
    public function saveMulti(array $vos = array());
}