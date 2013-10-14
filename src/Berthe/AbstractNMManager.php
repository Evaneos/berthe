<?php

/**
 * AbstractNMManager for N <-> M tables
 *
 * @author ghislain@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Berthe/AbstractNMManager.php
 * @package Berthe
 * @see Berthe/AbstractManager.php
 */
abstract class Berthe_AbstractNMManager extends Berthe_AbstractManager {
    /**
     *
     * @param Fetcher $paginator
     * @return Fetcher 
     */
    public function getByPaginator(Fetcher $paginator) {
        $paginator = $this->_getStorage()->getByPaginator($paginator);
        return $paginator;
    }
}