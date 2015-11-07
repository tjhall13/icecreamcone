<?php

function edit_links($links) { ?>
    <div><?php
    foreach($links as $link) { ?>
        <div>
            <input type="text" value="<?= $link['name']; ?>"><?php
        if(isset($link['url'])) { ?>
            <input type="text" value="<?= $link['url']; ?>">
            <button>Sublist</button><?php
        } else {
            edit_links($link['links']);
        } ?>
            <button>Delete</button>
        </div><?php
    } ?>
        <button>Add</button>
    </div><?php
}

edit_links($params['links']);

?>
