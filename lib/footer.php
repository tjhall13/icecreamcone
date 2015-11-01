<?php

namespace IceCreamCone;

class Footer extends Module {
    private $url;
    private $title;
    
    public function __construct($dbconn, $url, $title) {
        $this->url = $url;
        $this->title = $title;
    }
    
    public function html() {
        $params = array(
            'url' => $this->url,
            'title' => $this->title
        );
        include(THEME_PATH . 'core/footer.tpl.php');
    }
    
    public function json() {
        $result = array(
            'url' => $this->url,
            'title' => $this->title
        );
        return json_encode($result);
    }
}

?>
