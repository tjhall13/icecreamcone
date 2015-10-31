<?php

class Blog extends \IceCreamCone\Module {
    private $defined = false;
    
    private $byline;
    private $date;
    private $text;
    
    public function __construct($dbconn, $id) {
        $stmt = $dbconn->prepare('SELECT title, byline, date, text FROM ' . DB_TABLE_PREFIX . 'blogs WHERE blog_id = ?;');
        if($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($title, $byline, $date, $text);
            
            if($stmt->fetch()) {
                $this->defined = true;
                
                $this->title = $title;
                $this->byline = $byline;
                $this->date = $date;
                $this->text = $text;
            }
            
            $stmt->close();
        }
    }
    
    public function html() {
        if($this->defined) {
            $params = array(
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
}

Blog::register('blog');

?>
