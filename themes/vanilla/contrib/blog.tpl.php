<?php
$date = new DateTime($params['date']);
$params['date'] = $date->format('M j, Y');

if($params['user']) { ?>
<ul class="nav nav-tabs">
    <li class="active"><a href="#view-post-<?= $params['id']; ?>" data-toggle="tab">View</a></li>
    <li><a href="#edit-post-<?= $params['id']; ?>" data-toggle="tab">Edit</a></li>
</ul>
<div class="tab-content">
    <div id="view-post-<?= $params['id']; ?>" class="tab-pane active"> <?php
} ?>
<div id="post-<?= $params['id']; ?>" class="post">
    <div class="post-header">
        <a href="<?= $params['url']; ?>"><h2><?= $params['title']; ?></h2></a>
    </div>
    <div class="post-body">
        <?= $params['text']; ?>
    </div>
    <div class="post-footer">
        <h4><?= $params['date']; ?></h4>
    </div>
</div> <?php
if($params['user']) { ?>
    </div>
    <div id="edit-post-<?= $params['id']; ?>" class="tab-pane">
        <h4>Edit</h4>
    </div>
</div> <?php
} ?>
