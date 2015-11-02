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
        </div>
    </div>
</nav>
