<?php

function test_url($str) {
    return preg_match('/^[a-z]+:\/\/[^ ]+/', $str);
}

function print_links($links, $current) {
    foreach($links as $link) {
        if(isset($link['url'])) { 
            if(test_url($link['url'])) {
                $url = $link['url'];
            } else {
                $url = SITE_BASE . $link['url'];
            } ?>
            <li <?= strcmp($current, $link['url']) == 0 ? 'class="active"' : ''; ?>><a href="<?= $url; ?>"><?= $link['name']; ?></a></li>
  <?php } else if(isset($link['links'])) { ?>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?= $link['name']; ?><span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu"><?php
                    print_links($link['links'], $current);
                ?>
                </ul>
            </li>
  <?php }
    }
}

?>
<nav class="navbar navbar-dark navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#homepage-navbar">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand"><?= $params['title']; ?></div>
        </div>
        <div class="collapse navbar-collapse" id="homepage-navbar">
            <ul class="nav navbar-nav">
            <?php
                print_links($params['links'], $params['url']);
            ?>
            </ul>
            <ul class="nav navbar-nav navbar-right"><?php
                if($params['user']) {
                    echo '<li><a href="' . SITE_BASE . 'edit/">Edit <span class="glyphicon glyphicon-edit"></span></a></li>';
                    echo '<li><a href="' . SITE_BASE . 'logout.php" data-action="logout">Log Out <span class="glyphicon glyphicon-log-out"></span></a></li>';
                } else {
                    echo '<li><a href="#" data-toggle="modal" data-target="#login">Log In <span class="glyphicon glyphicon-log-in"></span></a></li>';
                } ?>
            </ul>
        </div>
    </div>
</nav>
<div id="login" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Log In</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="username" class="control-label">Username:</label>
                        <input type="text" class="form-control" id="username">
                    </div>
                    <div class="form-group">
                        <label for="password" class="control-label">Password:</label>
                        <input type="password" class="form-control" id="password">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button role="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="<?= SITE_BASE . 'login.php'; ?>"data-action="login" role="button" class="btn btn-primary">Login</a>
            </div>
        </div>
    </div>
</div>
