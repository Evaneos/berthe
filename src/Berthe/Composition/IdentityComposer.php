<?php

namespace Berthe\Composition;

class IdentityComposer extends AbstractComposer
{

    public function compose(array $objects = array(), array $embededModels = array())
    {
        return $objects;
    }
}
