<?php
    session_start();

    if(isset($_SESSION["user"])) {
        header("Location: profile.php");
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/public/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/public/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/public/favicon-16x16.png">
    <link rel="manifest" href="assets/public/site.webmanifest">
    <link rel="stylesheet" href="assets/css/globals.css">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <title>Регистрация – Charm</title>
</head>
<body>
    <div class="center-container">
        <form class="auth-form" action="vendor/signup.php" method="post">
            <div class="form-logo">
                <img src="assets/public/logo.svg" width=96>
                <h1>CHARM</h1>
                <h3>Регистрация</h3>
            </div>
            <input type="text" name="first_name" placeholder="Имя" required>
            <input type="text" name="middle_name" placeholder="Среднее имя">
            <input type="text" name="last_name" placeholder="Фамилия" required>

            <input type="email" name="email" placeholder="Электронная почта" required>
            <input type="password" name="password" placeholder="Пароль" required>

            <button type="submit">Зарегистрироваться</button>
            <p class="auth-hint">Уже есть аккаунт? <a href="login.php">Войдите</a></p>
        </form>
    </div>
    <footer>
        <p>© 2024 Государственный банк Эсептии</p>
        <a class="mailto" href="mailto:statebank@eseptia.site">statebank@eseptia.site</a>
    </footer>
</body>
</html>