<?php

namespace Berthe\DAL;

interface ComplexType
{
    /**
     * @return array array of query and params
     */
    function toDbRepresentation();
}