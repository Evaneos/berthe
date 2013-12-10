<?php

namespace Evaneos\Berthe;

abstract class AbstractHook {
    /**
     * Will be run before the hooked method
     * Should be abstract, but PHP won't fix the type hinting issue on abstract due to laziness (php bug #36601)
     * @return void
     */
    public function before($data) {

    }

    /**
     * Will be run after the hooked method
     * Should be abstract, but PHP won't fix the type hinting issue on abstract due to laziness (php bug #36601)
     * @return void
     */
    public function after($data) {

    }
}