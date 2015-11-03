<?php

header('Content-Type: application/json');

require('settings.php');
require('lib/database.php');

$username = $_POST['username'];
$password = $_POST['password'];

if($username && $password) {
    $dbconn = \IceCreamCone\dbconnect();
    $stmt = $dbconn->prepare('SELECT name, author_id, password FROM ' . DB_TABLE_PREFIX . 'authors WHERE username = ?;');
    if($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($name, $id, $actual);
        
        if($stmt->fetch()) {
            $hash = crypt($password, $actual);
            if(strcmp($hash, $actual) == 0) {
                session_start();
                $_SESSION['name'] = $name;
                $_SESSION['id'] = $id;
                $result = array('success' => true);
            } else {
                $result = array('error' => 'Username or password is wrong');
            }
        } else {
            $result = array('error' => 'Username or password is wrong');
        }
        
        $stmt->close();
    } else {
        $result = array('error' => $dbconn->error);
    }
    $dbconn->close();
} else {
    $result = array('error' => 'No username or password provided');
}

echo json_encode($result);

?>
