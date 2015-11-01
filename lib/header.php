<?php

namespace IceCreamCone;

class Header extends Module {
    private $defined = false;
    private $title;
    private $url;
    private $links;
    
    private function resolve($dbconn, $link_id) {
        $stmt = $dbconn->prepare('SELECT name, url, links.link_id FROM links JOIN (SELECT @r as _prev, @r := (SELECT link_id FROM links WHERE prev_id = _prev AND parent_id = ?) AS link_id FROM (SELECT @r := 0) vars, links) list ON links.link_id = list.link_id;');
        if($stmt) {
            $list = null;
            $stmt->bind_param('i', $link_id);
            if($stmt->execute()) {
                $list = array();
                $stmt->store_result();
            } else {
                throw new Exception($stmt->error);
            }
            $stmt->bind_result($name, $url, $id);
            
            while($stmt->fetch()) {
                if($url == 'NULL') {
                    $list[] = array(
                        'name' => $name,
                        'links' => $this->resolve($dbconn, $id)
                    );
                } else {
                    $list[] = array(
                        'name' => $name,
                        'url' => $url
                    );
                }
            }
            
            $stmt->close();
            return $list;
        } else {
            throw new Exception($dbconn->error);
        }
    }
    
    public function __construct($dbconn, $url, $title) {
        $this->title = $title;
        $this->url = $url;
        try {
            $this->links = $this->resolve($dbconn, 0);
            $this->defined = true;
        } catch(Exception $e) {
            $this->defined = false;
        }
    }
    
    public function html() {
        if($this->defined) {
            $params = array(
                'title' => $this->title,
                'url' => $this->url,
                'links' => $this->links
            );
            include(THEME_PATH . 'core/header.tpl.php');
        }
    }
    
    public function json() {
        if($this->defined) {
            $result = array(
                'title' => $this->title,
                'url' => $this->url,
                'links' => $this->links
            );
        } else {
            $result = array('error' => 'Could not retrieve links');
        }
        
        return json_encode($result);
    }
}

?>
