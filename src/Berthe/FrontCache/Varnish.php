<?php
class Berthe_FrontCache_Varnish extends Berthe_FrontCache_Abstract {
    protected static $instance = null;
    
    protected function _purgeURL($url) {
        $basicAuth = Zend_Registry::get(Initializer::CONFIG)->frontcachereloader->basicauth;
        $ba = "";
        if ($basicAuth->enabled) {
            $ba = "-u " . $basicAuth->username . ":" . $basicAuth->password;
        }
        
        $cmd = "curl -X " . $ba . " PURGE http://" . $url;
        $stdout = "/dev/null";
        $errout = "/dev/null";
        
        exec($cmd . " > " . $stdout . " 2> " . $errout . " &");
    }
}