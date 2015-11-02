<?php

header('Content-Type: application/json');

session_destroy();
setcookie('PHPSESSID', '');
echo json_encode(array('success' => true));

?>
