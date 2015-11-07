<?php

header('Content-Type: application/json');

session_start();
setcookie('PHPSESSID', '', time() - 3600);
session_destroy();
echo json_encode(array('success' => true));

?>
