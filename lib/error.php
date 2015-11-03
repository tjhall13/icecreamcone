<?php

namespace IceCreamCone;

class ErrorPage extends Module {
    private $code;
    private $url;
    private $user;
    
    public function __construct($code, $url) {
        $this->code = $code;
        $this->url = $url;
    }
    
    public function init($dbconn, $__ignore__, $params) {
        $this->user = $params['user'];
    }
    
    public function view() {
        $params = array(
            'code' => $this->code,
            'url' => $this->url,
            'user' => $this->user
        );
        include(THEME_PATH . 'core/error.tpl.php');
    }
    
    public function json() {
        $result = array(
            'code' => $this->code,
            'url' => $this->url
        );
        return json_encode($result);
    }
}

?>
