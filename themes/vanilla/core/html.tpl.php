<?php

if($params['user']) {
    echo __FILE__;
}

?>
<html>
    <head>
        <link rel="stylesheet" href="<?= SITE_BASE ?>bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?= SITE_BASE ?>css/homepage.min.css">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $params['title']; ?></title>
    </head>
    <body>
        <?php $params['header']->view(); ?>
        <?php $params['content']->view(); ?>
        <?php $params['footer']->view(); ?>
        
        <script src="<?= SITE_BASE ?>jquery/jquery-1.11.2.min.js"></script>
        <script src="<?= SITE_BASE ?>bootstrap/js/bootstrap.min.js"></script>
        <script src="<?= SITE_BASE ?>js/homepage.min.js"></script>
    </body>
</html>
