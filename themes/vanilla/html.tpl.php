<?php

?>
<html>
    <head>
        <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/homepage.min.css">
        
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= $params['title']; ?></title>
    </head>
    <body>
        <?php $params['header']->html(); ?>
        <?php $params['content']->html(); ?>
        <?php $params['footer']->html(); ?>
        
        <script src="/jquery/jquery-1.11.2.min.js"></script>
        <script src="/bootstrap/js/bootstrap.min.js"></script>
        <script src="/js/homepage.min.js"></script>
    </body>
</html>
