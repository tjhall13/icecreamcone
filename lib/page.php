<?php

namespace IceCreamCone;

class Page extends Module {
    private $status = 500;
    
    private $url;
    private $page_id;
    private $type;
    private $content_id;
    private $content;
    
    public function __construct($url, $title) {
        $this->url = $url;
        $this->title = $title;
    }
    
    public function init($dbconn, $__ignore__, &$params = array()) {
        $this->params = &$params;
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
                
                $class = Module::module($type);
                if($class != null) {
                    try {
                        $this->content = new $class();
                        $this->content->init($dbconn, $content_id, $params);
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
    }
    
    public function view() {
        if($this->status == 200) {
            $params = array(
                'url' => $this->url,
                'type' => $this->type,
                'content' => $this->content
            );
        } else {
            http_response_code($this->status);
            $page = new ErrorPage($this->status, $this->url);
            $params = array(
                'url' => $this->url,
                'type' => $this->type,
                'content' => $page
            );
        }
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
