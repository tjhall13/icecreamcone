<?php

require('error.php');

class Page {
    private $status = 500;
    
    private $url;
    private $page_id;
    private $type;
    private $content_id;
    
    public function __construct($dbconn, $url) {
        $this->url = $url;
        
        $stmt = $dbconn->prepare('SELECT page_id, type, content_id FROM Pages WHERE url = ?;');
        if($stmt) {
            $stmt->bind_param('s', $url);
            $stmt->execute();
            $stmt->bind_result($page_id, $type, $content_id);
            
            if($stmt->fetch()) {
                $this->status = 200;
                
                $this->type = $type;
                $this->page_id = $page_id;
                
                switch($type) {
                    case 'blog':
                        $this->get_blog($content_id);
                        break;
                    case 'article':
                        $this->get_article($content_id);
                        break;
                    case 'project':
                        $this->get_project($project_id);
                        break;
                }
            } else {
                $this->status = 404;
            }
            
            $stmt->close();
        }
    }
    
    public function html() {
        if($this->status == 200) {
            $params = array(
                'type' => $type,
                ''
            );
            include(THEME_PATH . 'page.tpl.php');
        } else {
            http_status_code($this->status);
            $page = new ErrorPage($this->status, $this->url);
            $page->html();
        }
    }
    
    public function json() {
        if($this->status == 200) {
            $result = array(
                
            );
        } else {
            $result = array('error' => $this->status);
        }
        return json_encode($result);
    }
}

?>
