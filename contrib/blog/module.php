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
    
    public function json() {
        if($this->defined) {
            $result = array(
                'id' => $this->blog_id,
                'title' => $this->title,
                'byline' => $this->byline,
                'date' => $this->date,
                'text' => $this->text,
                'url' => $this->url
            );
        } else {
            $result = array('error' => 'Unable to retrieve blog');
        }
        return json_encode($result);
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
}

Blog::register('blog');

?>
