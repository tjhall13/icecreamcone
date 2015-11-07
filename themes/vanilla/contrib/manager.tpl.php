<?php

function edit_links($links, $id = 0) { ?>
    <div class="link-group" data-link="<?= $id; ?>"><?php
    foreach($links as $link) { ?>
        <div class="form-inline form-group" data-link="<?= $link['id']; ?>">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="<?= $link['name']; ?>"><?php
        if(isset($link['links'])) { ?>
            <button class="btn btn-warning" data-action="save"><span class="glyphicon glyphicon-edit"></span></button>
            <button class="btn btn-default" data-action="node"><span class="glyphicon glyphicon-list"></span></button><?php
            edit_links($link['links'], $link['id']);
        } else { ?>
            <label for="url">URL</label>
            <input type="text" name="url" class="form-control" value="<?= $link['url']; ?>">
            <button class="btn btn-danger" data-action="remove"><span class="glyphicon glyphicon-minus-sign"></span></button>
            <button class="btn btn-warning" data-action="save"><span class="glyphicon glyphicon-edit"></span></button>
            <button class="btn btn-primary" data-action="leaf"><span class="glyphicon glyphicon-list"></span></button><?php
        } ?>
        </div><?php
    } ?>
        <div class="form-inline form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control">
            <label>URL</label>
            <input type="text" name="url" class="form-control">
            <button class="btn btn-success" data-action="add"><span class="glyphicon glyphicon-plus-sign"></span></button>
        </div>
    </div><?php
}

function edit_content($content) { ?>
    <table>
        <thead>
            <th>Page ID</th>
            <th>URL</th>
            <th>Type</th>
            <th>Content ID</th>
        </thead>
        <tbody><?php
        foreach($content as $item) { ?>
            <tr>
                <td><?= $item['page_id']; ?></td>
                <td><input type="text" class="form-control" value="<?= $item['url']; ?>"></td>
                <td><input type="text" class="form-control" value="<?= $item['type']; ?>"></td>
                <td><input type="text" class="form-control" value="<?= $item['content_id']; ?>"></td>
                <td>
                    <button class="btn btn-danger" data-action="remove"><span class="glyphicon glyphicon-minus-sign"></span></button>
                    <button class="btn btn-warning" data-action="save"><span class="glyphicon glyphicon-edit"></span></button>
                </td>
            </tr><?php
        } ?>
            <tr>
                <td></td>
                <td><input type="text" class="form-control"></td>
                <td><input type="text" class="form-control"></td>
                <td><input type="text" class="form-control"></td>
                <td>
                    <button class="btn btn-success" data-action="add"><span class="glyphicon glyphicon-plus-sign"></span></button>
                </td>
            </tr>
        </tbody>
    </table><?php
}

?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <h2>Navigation Links</h2>
        <div class="link-editor"><?php
            edit_links($params['links']); ?>
        </div>
        <h2>Content</h2>
        <h4>You really shouldn't be messing with these options...</h4>
        <div class="content-editor"><?php
            edit_content($params['content']); ?>
        </div>
        <h4 class="pull-right">...I would hate to say I told you so.</h4>
    </div>
</div>
