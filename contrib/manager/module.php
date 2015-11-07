<?php

namespace IceCreamCone;

class Manager extends Module {
    private $path;
    private $links;
    private $content;
    private $user;
    
    public function __construct() {
        $path = THEME_PATH . 'contrib/manager.tpl.php';
        if(file_exists($path)) {
            $this->path = $path;
        } else {
            $this->path = __DIR__ . '/default.tpl.php';
        }
    }
    
    public function init($dbconn, $__ignore__, &$params = array()) {
        if($params['user']) {
            $this->user = $params['user'];
            $this->links = $params['links'];
            $this->content = array();
            $stmt = $dbconn->prepare('SELECT page_id, url, type, content_id FROM ' . DB_TABLE_PREFIX . 'pages;');
            if($stmt) {
                $stmt->execute();
                $stmt->bind_result($page_id, $url, $type, $content_id);
                while($stmt->fetch()) {
                    $this->content[] = array('page_id' => $page_id, 'url' => $url, 'type' => $type, 'content_id' => $content_id);
                }
                $stmt->close();
            }
        } else {
            throw new \Exception('unauthorized');
        }
    }
    
    public function view() {
        $params = array(
            'links' => $this->links,
            'content' => $this->content
        );
        include($this->path);
    }
    
    public function edit($dbconn, $method, $params) { }
}

Manager::register('manager');

?>
