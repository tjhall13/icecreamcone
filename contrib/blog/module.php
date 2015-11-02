<?php

class Blog extends \IceCreamCone\Module {
    private $defined = false;
    
    private $id;
    private $title;
    private $byline;
    private $date;
    private $text;
    
    private $path;
    
    public function __construct($dbconn, $id, $params = array()) {
        $path = THEME_PATH . 'contrib/blog.tpl.php';
        if(file_exists($path)) {
            $this->path = $path;
        } else {
            $this->path = __DIR__ . '/default.tpl.php';
        }
        
        $stmt = $dbconn->prepare('SELECT ' . DB_TABLE_PREFIX . 'blogs.title, ' . DB_TABLE_PREFIX . 'authors.name, ' . DB_TABLE_PREFIX . 'blogs.date, ' . DB_TABLE_PREFIX . 'blogs.text FROM ' . DB_TABLE_PREFIX . 'blogs LEFT JOIN ' . DB_TABLE_PREFIX . 'authors ON ' . DB_TABLE_PREFIX . 'blogs.author_id = ' . DB_TABLE_PREFIX . 'authors.author_id WHERE ' . DB_TABLE_PREFIX . 'blogs.blog_id = ?;');
        if($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($title, $byline, $date, $text);
            
            if($stmt->fetch()) {
                $this->defined = true;
                
                $this->id = $id;
                $this->title = $title;
                $this->byline = $byline;
                $this->date = $date;
                $this->text = $text;
            }
            
            $stmt->close();
        } else {
            echo $dbconn->error;
        }
    }
    
    public function html() {
        if($this->defined) {
            $params = array(
                'id' => $this->id,
                'title' => $this->title,
                'byline' => $this->byline,
                'date' => $this->date,
                'text' => $this->text
            );
            include($this->path);
        } else {
            echo '<div class="post-failed"></div>';
        }
    }
    
    public function json() {
        if($this->defined) {
            $result = array(
                'id' => $this->id,
                'title' => $this->title,
                'byline' => $this->byline,
                'date' => $this->date,
                'text' => $this->text
            );
        } else {
            $result = array('error' => 'Unable to retrieve blog');
        }
        return json_encode($result);
    }
    
    public function id() {
        return $this->id;
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
}

Blog::register('blog');

?>
