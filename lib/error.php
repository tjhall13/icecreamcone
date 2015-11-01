<?php

namespace IceCreamCone;

class ErrorPage extends Module {
    private $code;
    private $url;
    
    public function __construct($code, $url) {
        $this->code = $code;
        $this->url = $url;
    }
    
    public function html() {
        $params = array(
            'code' => $this->code,
            'url' => $this->url
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
