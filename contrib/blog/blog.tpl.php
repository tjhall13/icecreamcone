<?php

$date = new DateTime($params['date']);
$params['date'] = $date->format('M j, Y');

?>
<div id="post-<?= $params['id']; ?>" class="post">
    <div class="post-header">
        <h2><?= $params['title']; ?></h2>
    </div>
    <div class="post-body">
        <?= $params['text']; ?>
    </div>
    <div class="post-footer">
        <h4><?= $params['date']; ?></h4>
    </div>
</div>
