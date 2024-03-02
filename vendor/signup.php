<?php

    session_start();
    require_once 'connect.php';

    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $connect->prepare('INSERT INTO users(first_name, middle_name, last_name, email, password) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $first_name, $middle_name, $last_name, $email, $password_hash);
    $stmt->execute();

    $_SESSION["message_info"] = "Аккаунт успешно создан!";

    header('Location: ../login.php');