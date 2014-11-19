<?php

namespace Berthe\Composition;

interface ComposerProvider
{
    /**
     * @param string $name
     * @return AbstractComposer
     */
    public function getComposer($name);
}
