<?php

class Blog extends \IceCreamCone\Module {
    private $defined = false;
    
    private $id;
    private $title;
    private $byline;
    private $date;
    private $text;
    
    public function __construct($dbconn, $id) {
        $stmt = $dbconn->prepare('SELECT blogs.title, authors.name, blogs.date, blogs.text FROM ' . DB_TABLE_PREFIX . 'blogs LEFT JOIN ' . DB_TABLE_PREFIX . 'authors ON blogs.author_id = authors.author_id WHERE blog_id = ?;');
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
            include(__DIR__ . '/blog.tpl.php');
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
