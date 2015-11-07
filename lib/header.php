<?php

namespace IceCreamCone;

class Header extends Module {
    private $defined = false;
    private $url;
    private $links = array();
    private $user;
    private $title;
    
    public static function links($dbconn, $link_id = 0) {
        $stmt = $dbconn->prepare('SELECT name, url, ' . DB_TABLE_PREFIX . 'links.link_id, ' . DB_TABLE_PREFIX . 'links.link_id IN (SELECT parent_id FROM ' . DB_TABLE_PREFIX . 'links) FROM ' . DB_TABLE_PREFIX . 'links RIGHT JOIN (SELECT @r as _prev, @r := (SELECT link_id FROM ' . DB_TABLE_PREFIX . 'links WHERE prev_id = _prev AND parent_id = ?) AS link_id FROM (SELECT @r := 0) vars, ' . DB_TABLE_PREFIX . 'links) list ON ' . DB_TABLE_PREFIX . 'links.link_id = list.link_id;');
        if($stmt) {
            $list = null;
            $stmt->bind_param('i', $link_id);
            if($stmt->execute()) {
                $list = array();
                $stmt->store_result();
            } else {
                throw new \Exception($stmt->error);
            }
            $stmt->bind_result($name, $url, $id, $parent);
            
            while($stmt->fetch() && $id !== null) {
                $link = array(
                    'id' => $id,
                    'name' => $name,
                    'url' => $url
                );
                if($parent) {
                    $link['links'] = self::links($dbconn, $id);
                }
                $list[] = $link;
            }
            
            $stmt->close();
            return $list;
        } else {
            throw new \Exception($dbconn->error);
        }
    }
    
    public function __construct($url, $title) {
        $this->url = $url;
        $this->title = $title;
    }
    
    public function init($dbconn, $__ignore__, &$params = array()) {
        $this->user = $params['user'];
        $this->links = $params['links'];
        $this->defined = true;
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
    
    private function detach($dbconn, $id) {
        $stmt = $dbconn->prepare('SELECT prev_id FROM ' . DB_TABLE_PREFIX . 'links WHERE link_id = ?;');
        if($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($prev_id);
            
            if(!$stmt->fetch()) {
                throw new \Exception('link does not exist');
            }
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
        $stmt = $dbconn->prepare('SELECT link_id FROM ' . DB_TABLE_PREFIX . 'links WHERE prev_id = ?;');
        if($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($post_id);
            if(!$stmt->fetch()) {
                $post_id = NULL;
            }
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
        if($post_id != NULL) {
            $stmt = $dbconn->prepare('UPDATE ' . DB_TABLE_PREFIX . 'links SET prev_id = ? WHERE link_id = ?;');
            if($stmt) {
                $stmt->bind_param('ii', $prev_id, $post_id);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new \Exception($dbconn->error);
            }
        }
    }
    
    private function add($dbconn, $parent, $name, $url) {
        $stmt = $dbconn->prepare('SELECT link_id FROM ' . DB_TABLE_PREFIX . 'links WHERE parent_id = ? AND link_id NOT IN (SELECT prev_id FROM ' . DB_TABLE_PREFIX . 'links);');
        if($stmt) {
            $stmt->bind_param('i', $parent);
            $stmt->execute();
            $stmt->bind_result($prev_id);
            
            if(!$stmt->fetch()) {
                $prev_id = 0;
            }
            
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
        $stmt = $dbconn->prepare('INSERT INTO ' . DB_TABLE_PREFIX . 'links (name, url, parent_id, prev_id) VALUES (?, ?, ?, ?);');
        if($stmt) {
            $stmt->bind_param('ssii', $name, $url, $parent, $prev_id);
            if(!$stmt->execute()) {
                throw new \Exception('unable to add link');
            }
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
    }
    
    private function remove($dbconn, $id) {
        $this->detach($dbconn, $id);
        
        $stmt = $dbconn->prepare('DELETE FROM ' . DB_TABLE_PREFIX . 'links WHERE link_id = ?;');
        if($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
    }
    
    private function set($dbconn, $id, $params) {
        $update = false;
        $query = 'UPDATE ' . DB_TABLE_PREFIX . 'links SET ';
        $args = array('');
        if(property_exists($params, 'name')) {
            $update = true;
            $query .= 'name = ?, ';
            $args[0] .= 's';
            $args[] = &$params->name;
        }
        if(property_exists($params, 'url')) {
            $update = true;
            $query .= 'url = ?, ';
            $args[0] .= 's';
            $args[] = &$params->url;
        }
        if(property_exists($params, 'parent') && property_exists($params, 'previous')) {
            $this->detach($dbconn, $id);
            
            // Move new subsequent link to after current link
            $stmt = $dbconn->prepare('UPDATE ' . DB_TABLE_PREFIX . 'links SET prev_id = ? WHERE prev_id = ? AND parent_id = ?;');
            if($stmt) {
                $stmt->bind_param('iii', $id, $params->previous, $params->parent);
                $stmt->execute();
                $stmt->close();
            } else {
                throw new \Exception($dbconn->error);
            }
            
            // Update current link's parent and previous
            $update = true;
            $query .= 'parent_id = ?, prev_id = ?, ';
            $args[0] .= 'ii';
            $args[] = &$params->parent;
            $args[] = &$params->previous;
        }
        
        if($update) {
            $query = substr($query, 0, strlen($query) - 2) . ' WHERE link_id = ?;';
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
                $this->add($dbconn, $params->parent, $params->name, $params->url);
                break;
            case strcmp($method, 'remove'):
                $this->remove($dbconn, $params->id);
                break;
            case strcmp($method, 'set'):
                $this->set($dbconn, $params->id, $params);
                break;
            default:
                throw new \Exception('unrecognized method');
                break;
        }
    }
}

?>
