<?php

namespace IceCreamCone;

abstract class Module {
    private static $types = array();
    public static function register($type) {
        if(isset(self::$types[$type])) {
            return false;
        } else {
            self::$types[$type] = static::class;
            return true;
        }
    }
    public static function module($type) {
        return self::$types[$type];
    }
    
    protected $params;
    
    public function init($dbconn, $content_id, &$params) {
        $this->params = &$params;
    }
    
    abstract public function view();
    abstract public function json();
}

?>
