<?php

require('settings.php');

require('lib/database.php');
require('lib/header.php');
require('lib/page.php');
require('lib/footer.php');

$url = $_GET['url'];

if(substr($url, -1) != '/' && $url != '') {
    header('Location: ' . $url . '/');
} else {
    $dbconn = dbconnect();

    $header = new Header($dbconn, $url);
    $page = new Page($dbconn, $url);
    $footer = new Footer($dbconn, $url);

    $params = array(
        'title' => 'Trevor Hall',
        'header' => $header,
        'content' => $page,
        'footer' => $footer
    );
    include(THEME_PATH . 'html.tpl.php');

    $dbconn->close();
}

?>
