<?php

function dbconnect($dbname = DB_NAME, $dbhost = DB_HOST, $dbuser = DB_USERNAME, $dbpass = DB_PASSWORD) {
    $host = explode(':', $dbhost);
    if(count($host) > 1) {
        return new mysqli($host[0], $dbuser, $dbpass, $dbname, $host[1]);
    } else {
        return new mysqli($host[0], $dbuser, $dbpass, $dbname);
    }
}

?>
