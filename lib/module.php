<?php

namespace IceCreamCone;

abstract class Module {
    private static $types = array();
    public static function register($type) {
        if(isset(self::$types[$type])) {
            return false;
        } else {
            self::$types[$type] = get_called_class();
            return true;
        }
    }
    public static function module($type) {
        return self::$types[$type];
    }
    
    public function init($dbconn, $content_id, &$params) { }
    
    abstract public function view();
    abstract public function json();
}

?>
