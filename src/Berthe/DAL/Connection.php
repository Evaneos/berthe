<?php

namespace Berthe\DAL;

interface Connection
{
    public function query($sql, $bind = array());

    public function getAdapter();
}