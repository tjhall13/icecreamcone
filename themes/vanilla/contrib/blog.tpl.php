<?php
$date = (new DateTime($params['date']))->format('M j, Y');

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
        <h4><?= $date; ?></h4>
    </div>
</div> <?php
if($params['user']) { ?>
    </div>
    <div id="edit-post-<?= $params['id']; ?>" class="tab-pane">
        <form>
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" class="form-control" name="title" value="<?= $params['title']; ?>">
            </div>
            <div class="form-group">
                <label for="date">Date</label>
                <input type="text" class="form-control" name="date" value="<?= $params['date']; ?>">
            </div>
            <div class="form-group">
                <label for="date">Text</label>
                <?= $params['editor']; ?>
            </div>
            <button type="submit" class="btn btn-primary pull-right">Save</button>
        </form>
    </div>
</div> <?php
} ?>
