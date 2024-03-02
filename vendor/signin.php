<?php

    session_start();
    require_once 'connect.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $connect->prepare('SELECT * FROM `users` WHERE `email` = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $result = $stmt->get_result();
    if (mysqli_num_rows($result) <= 0) {
        $_SESSION["message_err"] = "Аккаунт не найден";
        header("Location: ../login.php");
        exit;
    }

    $row = $result->fetch_assoc();
    if(!password_verify($password, $row["password"])) {
        $_SESSION["message_err"] = "Неверный пароль";
        header("Location: ../login.php");
        exit;
    }

    $_SESSION["user"] = [
        "id" => $row["id"],
        "email" => $row["email"],
    ];

    header("Location: ../profile.php");

   