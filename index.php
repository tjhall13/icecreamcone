<?php

define('CONTRIB_PATH', __DIR__ . '/contrib/');
define('CUSTOM_PATH', __DIR__ . '/custom/');

require('settings.php');
require('lib/database.php');

require('lib/module.php');

// Core Includes
require('lib/header.php');
require('lib/page.php');
require('lib/footer.php');
require('lib/error.php');

// Contrib Includes
$modules = glob(CONTRIB_PATH . '**/module.php');
foreach($modules as $module) {
    include($module);
}

// Custom Includes
$modules = glob(CUSTOM_PATH . '**/module.php');
foreach($modules as $module) {
    include($module);
}

$url = $_GET['url'];

if(strcmp($url, '') != 0 && strcmp(substr($url, -1), '/') != 0) {
    header('Location: ' . $url . '/');
} else {
    $dbconn = \IceCreamCone\dbconnect();

    $title = SITE_AUTHOR;

    $header = new \IceCreamCone\Header($dbconn, $url, $title);
    $page = new \IceCreamCone\Page($dbconn, $url, $title);
    $footer = new \IceCreamCone\Footer($dbconn, $url, $title);

    $params = array(
        'title' => $title,
        'header' => $header,
        'content' => $page,
        'footer' => $footer
    );
    include(THEME_PATH . 'html.tpl.php');

    $dbconn->close();
}

?>
