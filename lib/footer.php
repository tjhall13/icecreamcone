<?php

namespace IceCreamCone;

class Footer extends Module {
    private $url;
    private $title;
    private $author;
    private $user;
    
    public function __construct($url, $title) {
        $this->url = $url;
        $this->title = $title;
        $this->author = SITE_AUTHOR;
    }
    
    public function init($dbconn, $__ignore__, &$params = array()) {
        $this->user = $params['user'];
    }
    
    public function view() {
        $params = array(
            'url' => $this->url,
            'title' => $this->title,
            'author' => $this->author,
            'user' => $this->user
        );
        include(THEME_PATH . 'core/footer.tpl.php');
    }
    
    public function json() {
        $result = array(
            'url' => $this->url,
            'title' => $this->title,
            'author' => $this->author
        );
        return json_encode($result);
    }
}

?>
