<?php
    session_start();
    unset($_SESSION["user"]);
    $_SESSION["message_info"] = "Вы вышли из аккаунта";
    header("Location: ../login.php");