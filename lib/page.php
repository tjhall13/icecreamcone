<?php

namespace IceCreamCone;

class Page extends Module {
    private $status = 500;
    
    private $url;
    private $page_id;
    private $type;
    private $content_id;
    private $content;
    private $user;
    
    public function __construct($url, $title) {
        $this->url = $url;
        $this->title = $title;
    }
    
    public function init($dbconn, $__ignore__, &$params = array()) {
        $stmt = $dbconn->prepare('SELECT page_id, type, content_id FROM ' . DB_TABLE_PREFIX . 'pages WHERE url = ' . ($this->url == '' ? "''" : '?') . ';');
        if($stmt) {
            if($this->url != '') {
                $stmt->bind_param('s', $this->url);
            }
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($page_id, $type, $content_id);
            
            if($stmt->fetch()) {
                $this->type = $type;
                $this->page_id = $page_id;
                $this->user = $params['user'];
                
                $class = Module::module($type);
                if($class != null) {
                    try {
                        $this->content = new $class();
                        $args = array(
                            'user' => $params['user'],
                            'title' => &$params['title']
                        );
                        $this->content->init($dbconn, $content_id, $args);
                        $this->status = 200;
                    } catch(Exception $e) {
                        error_log($e->getMessage());
                    }
                }
            } else {
                $this->status = 404;
            }
            
            $stmt->close();
        } else {
            error_log($dbconn->error);
        }
        
        if($this->status != 200) {
            $this->content = new ErrorPage($this->status, $this->url);
            http_response_code($this->status);
        }
    }
    
    public function view() {
        $params = array(
            'url' => $this->url,
            'type' => $this->type,
            'content' => $this->content,
            'user' => $this->user
        );
        include(THEME_PATH . 'core/page.tpl.php');
    }
    
    public function json() {
        if($this->status == 200) {
            $result = array(
                'url' => $this->url,
                'type' => $this->type,
                'content' => $this->content
            );
        } else {
            $result = array('error' => $this->status);
        }
        return json_encode($result);
    }
}

?>
