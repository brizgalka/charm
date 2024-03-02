<?php

    require_once 'config.php'; 

    $connect = mysqli_connect($database_url, $database_user, $database_pass, $database_name);

    if (!$connect) { 
        die('Unable to connect to the database');
    }