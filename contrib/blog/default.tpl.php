<?php
$date = new DateTime($params['date']);
$params['date'] = $date->format('m/d/Y');
?>
<div class="blog">
    <div class="blog-title">
        <h2><?= $params['title']; ?></h2>
    </div>
    <div class="blog-byline">
        <h4><?= $params['byline']; ?></h4>
        <h4><?= $params['date']; ?></h4>
    </div>
    <div class="blog-body">
        <?= $params['text']; ?>
    </div>
</div>
