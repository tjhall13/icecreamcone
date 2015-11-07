<?php

class Blog extends \IceCreamCone\Module {
    private $defined = false;
    
    private $blog_id;
    private $title;
    private $byline;
    private $date;
    private $text;
    private $user;
    private $url;
    private $editor;
    
    private $path;
    
    public function __construct() {
        $path = THEME_PATH . 'contrib/blog.tpl.php';
        if(file_exists($path)) {
            $this->path = $path;
        } else {
            $this->path = __DIR__ . '/default.tpl.php';
        }
        $this->url = '#';
    }
    
    public function init($dbconn, $blog_id, &$params = array('editors' => array())) {
        $stmt = $dbconn->prepare('SELECT ' . DB_TABLE_PREFIX . 'blogs.title, ' . DB_TABLE_PREFIX . 'authors.name, ' . DB_TABLE_PREFIX . 'blogs.date, ' . DB_TABLE_PREFIX . 'blogs.text FROM ' . DB_TABLE_PREFIX . 'blogs LEFT JOIN ' . DB_TABLE_PREFIX . 'authors ON ' . DB_TABLE_PREFIX . 'blogs.author_id = ' . DB_TABLE_PREFIX . 'authors.author_id WHERE ' . DB_TABLE_PREFIX . 'blogs.blog_id = ?;');
        if($stmt) {
            $stmt->bind_param('i', $blog_id);
            $stmt->execute();
            $stmt->bind_result($title, $byline, $date, $text);
            
            if($stmt->fetch()) {
                $this->defined = true;
                $html_id = 'edit-blog-' . $blog_id;
                
                $this->blog_id = $blog_id;
                $this->title = $title;
                $this->byline = $byline;
                $this->date = $date;
                $this->text = $text;
                $this->user = $params['user'];
                $this->editor = '<textarea id="' . $html_id . '" rows="20" cols="80">' . $text . '</textarea>';
                
                $params['editors'][] = $html_id;
                $params['title'] = $title;
            }
            
            $stmt->close();
        } else {
            error_log($dbconn->error);
        }
        $stmt = $dbconn->prepare('SELECT url FROM ' . DB_TABLE_PREFIX . 'pages WHERE type = ? AND content_id = ?;');
        if($stmt) {
            $type = 'blog';
            $stmt->bind_param('si', $type, $blog_id);
            $stmt->execute();
            $stmt->bind_result($url);
            
            if($stmt->fetch()) {
                $this->url = SITE_BASE . $url;
            }
            
            $stmt->close();
        } else {
            error_log($dbconn->error);
        }
        
    }
    
    public function id() {
        return $this->blog_id;
    }
    
    public function title() {
        return $this->title;
    }
    
    public function byline() {
        return $this->byline;
    }
    
    public function date() {
        return $this->date;
    }
    
    public function text() {
        return $this->text;
    }
    
    public function url() {
        return $this->url;
    }
    
    public function view() {
        if($this->defined) {
            $params = array(
                'id' => $this->blog_id,
                'title' => $this->title,
                'byline' => $this->byline,
                'date' => $this->date,
                'text' => $this->text,
                'user' => $this->user,
                'url' => $this->url,
                'editor' => $this->editor
            );
            include($this->path);
        } else {
            echo '<div class="post-failed"></div>';
        }
    }
    
    private function set($dbconn, $id, $params) {
        $query = 'UPDATE ' . DB_TABLE_PREFIX . 'blogs SET ';
        $args = array('');
        $update = false;
        
        if(property_exists($params, 'title')) {
            $query .= 'title = ?, ';
            $args[0] .= 's';
            $args[] = &$params->title;
            $update = true;
        }
        
        if(property_exists($params, 'date')) {
            $query .= 'date = ?, ';
            $args[0] .= 's';
            $args[] = &$params->date;
            $update = true;
        }
        
        if(property_exists($params, 'author')) {
            $query .= 'author_id = ?, ';
            $args[0] .= 'i';
            $args[] = &$params->author;
            $update = true;
        }
        
        if(property_exists($params, 'text')) {
            $query .= 'text = ?, ';
            $args[0] .= 's';
            $args[] = &$params->text;
            $update = true;
        }
        
        if($update) {
            $query = substr($query, 0, strlen($query) - 2) . ' WHERE blog_id = ?;';
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
    
    private function remove($dbconn, $id) {
        $stmt = $dbconn->prepare('DELETE FROM ' . DB_TABLE_PREFIX . 'blogs WHERE blog_id = ?;');
        if($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
        
        $stmt = $dbconn->prepare('DELETE FROM ' . DB_TABLE_PREFIX . 'pages WHERE content_id = ? AND type = ?;');
        if($stmt) {
            $type = 'blog';
            $stmt->bind_param('is', $id, $type);
            $stmt->execute();
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
    }
    
    private function add($dbconn, $params) {
        $title = $params->title;
        $date = $params->date;
        $text = $params->text;
        $url = $params->url;
        $author = $params->user_id;
        
        $stmt = $dbconn->prepare('INSERT INTO ' . DB_TABLE_PREFIX . 'blogs (title, date, text, author_id) VALUES (?, ?, ?, ?);');
        if($stmt) {
            $stmt->bind_param('sssi', $title, $date, $text, $author);
            $stmt->execute();
            
            $type = 'blog';
            $content_id = $dbconn->insert_id;
            
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
        
        $stmt = $dbconn->prepare('INSERT INTO ' . DB_TABLE_PREFIX . 'pages (url, type, content_id) VALUES (?, ?, ?);');
        if($stmt) {
            $stmt->bind_param('ssi', $url, $type, $content_id);
            if(!$stmt->execute()) {
                $error = $stmt->error;
                $stmt->close();
                throw new \Exception($error);
            }
            $stmt->close();
        } else {
            throw new \Exception($dbconn->error);
        }
    }
    
    public function edit($dbconn, $method, $params) {
        switch(0) {
            case strcmp($method, 'add'):
                $this->add($dbconn, $params);
                break;
            case strcmp($method, 'set'):
                $this->set($dbconn, $params->id, $params);
                break;
            case strcmp($method, 'remove'):
                $this->remove($dbconn, $params->id);
                break;
        }
    }
}

Blog::register('blog');

?>
