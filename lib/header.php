<?php

namespace IceCreamCone;

class Header extends Module {
    private $defined = false;
    private $url;
    private $links;
    private $user;
    private $title;
    
    private function resolve($dbconn, $link_id) {
        $stmt = $dbconn->prepare('SELECT name, url, ' . DB_TABLE_PREFIX . 'links.link_id FROM ' . DB_TABLE_PREFIX . 'links JOIN (SELECT @r as _prev, @r := (SELECT link_id FROM ' . DB_TABLE_PREFIX . 'links WHERE prev_id = _prev AND parent_id = ?) AS link_id FROM (SELECT @r := 0) vars, ' . DB_TABLE_PREFIX . 'links) list ON ' . DB_TABLE_PREFIX . 'links.link_id = list.link_id;');
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
    
    public function __construct($url, $title) {
        $this->url = $url;
        $this->title = $title;
    }
    
    public function init($dbconn, $__ignore__, &$params = array()) {
        try {
            $this->links = $this->resolve($dbconn, 0);
            $this->user = $params['user'];
            $this->defined = true;
        } catch(Exception $e) {
            $this->defined = false;
        }
    }
    
    public function view() {
        if($this->defined) {
            $params = array(
                'title' => $this->title,
                'user' => $this->user,
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
