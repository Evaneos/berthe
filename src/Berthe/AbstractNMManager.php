<?php

/**
 * AbstractNMManager for N <-> M tables
 *
 * @author ghislain@evaneos.com
 * @copyright Evaneos
 * @version 1.0 
 * @filesource Evaneos/Berthe/AbstractNMManager.php
 * @package Evaneos/Berthe
 * @since Berthe
 * @see Evaneos/Berthe/AbstractManager.php
 */
abstract class Evaneos_Berthe_AbstractNMManager extends Evaneos_Berthe_AbstractManager {
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