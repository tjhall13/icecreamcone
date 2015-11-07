<?php

require('settings.php');
require('lib/database.php');

require('lib/module.php');

// Core Includes
require('lib/header.php');
require('lib/page.php');
require('lib/footer.php');

define('CONTRIB_PATH', __DIR__ . '/contrib/');
define('CUSTOM_PATH', __DIR__ . '/custom/');

function handle_requests($dbconn, $data, $user_id) {
    $dbconn->begin_transaction();
    foreach($data as $type => $requests) {
        foreach($requests as $request) {
            $module = null;
            switch(0) {
                case strcmp($type, 'links'):
                    $module = new \IceCreamCone\Header('api.php', 'API');
                    break;
                case strcmp($type, 'pages'):
                    $module = new \IceCreamCone\Page('api.php', 'API');
                    break;
                default:
                    $class = \IceCreamCone\Module::module($type);
                    if($class) {
                        $module = new $class();
                    } else {
                        return array('error' => 'unknown type');
                    }
                    break;
            }
            try {
                $request->params->user_id = $user_id;
                $module->edit($dbconn, $request->method, $request->params);
            } catch(Exception $e) {
                $msg = $e->getFile() . '[' . $e->getLine() . ']: ' . $e->getMessage();
                error_log($msg);
                $dbconn->rollback();
                return array('error' => $msg);
            }
        }
    }
    $dbconn->commit();
    return array('success' => true);
}

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

header('Content-Type: application/json');

$data = json_decode($_POST['data']);

session_start();
if(isset($_SESSION['name']) && isset($_SESSION['id'])) {
    $dbconn = \IceCreamCone\dbconnect();
    $dbconn->autocommit(false);
    $result = handle_requests($dbconn, $data, $_SESSION['id']);
    $dbconn->close();
} else {
    http_response_code(401);
    $result = array('error' => 'unauthorized');
}

echo json_encode($result);

?>
