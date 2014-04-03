<?php

namespace Berthe\Fetcher;

use Berthe\Fetcher as Fetcher;

interface Fetchable
{
    function fetch(Fetcher $fetcher);
}