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
                            'links' => $params['links'],
                            'title' => &$params['title'],
                            'editors' => &$params['editors']
                        );
                        $this->content->init($dbconn, $content_id, $args);
                        $this->status = 200;
                    } catch(\Exception $e) {
                        if(strcmp($e->getMessage(), 'unauthorized') == 0) {
                            $this->status = 401;
                        } else {
                            error_log($e->getMessage());
                        }
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
    
    private function add($dbconn, $params) {
        if(property_exists($params, 'url') && property_exists($params, 'type') && property_exists($params, 'content')) {
            $stmt = $dbconn->prepare('INSERT INTO ' . DB_TABLE_PREFIX . 'pages (url, type, content_id) VALUES (?, ?, ?);');
            if($stmt) {
                $stmt->bind_param('ssi', $params->url, $params->type, $params->content);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new \Excpetion($dbconn->error);
            }
        } else {
            throw new \Excpetion('Missing parameters');
        }
    }
    
    private function remove($dbconn, $id) {
        $stmt = $dbconn->prepare('DELETE FROM ' . DB_TABLE_PREFIX . 'pages WHERE page_id = ?;');
        if($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
    }
    
    private function set($dbconn, $id, $params) {
        $query = 'UPDATE ' . DB_TABLE_PREFIX . 'pages SET ';
        $args = array('');
        $update = false;
        
        if(property_exists($params, 'url')) {
            $query .= 'url = ?, ';
            $args[0] .= 's';
            $args[] = &$params->url;
            $update = true;
        }
        
        if(property_exists($params, 'type')) {
            $query .= 'type = ?, ';
            $args[0] .= 's';
            $args[] = &$params->type;
            $update = true;
        }
        
        if(property_exists($params, 'content')) {
            $query .= 'content_id = ?, ';
            $args[0] .= 'i';
            $args[] = &$params->content;
            $update = true;
        }
        
        if($update) {
            $query = substr($query, 0, strlen($query) - 2) . ' WHERE page_id = ?;';
            $args[0] .= 'i';
            $args[] = &$id;
            
            $stmt = $dbconn->prepare($query);
            if($stmt) {
                call_user_func_array(array($stmt, 'bind_param'), $args);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new \Exception($dbconn->error);
            }
        }
    }
    
    public function edit($dbconn, $method, $params) {
        switch(0) {
            case strcmp($method, 'add'):
                $this->add($dbconn, $params);
                break;
            case strcmp($method, 'remove'):
                $this->remove($dbconn, $params->id);
                break;
            case strcmp($method, 'set'):
                $this->set($dbconn, $params->id, $params);
                break;
        }
    }
}

?>
