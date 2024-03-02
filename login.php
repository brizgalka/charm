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
    <title>Войти – Charm</title>
</head>
<body>
    <div class="center-container">
        <form class="auth-form" action="vendor/signin.php" method="post">
            <div class="form-logo">
                <img src="assets/public/logo.svg" width=96>
                <h1>CHARM</h1>
                <h3>Авторизация</h3>
            </div>
            
            <input type="email" name="email" placeholder="Электронная почта">
            <input type="password" name="password" placeholder="Пароль">

            <button type="submit">Войти</button>

            <?php
                if(isset($_SESSION["message_err"])) {
                    echo "<p class='message_err'>{$_SESSION['message_err']}</p>";
                } else if (isset($_SESSION["message_info"])) {
                    echo "<p class='message_info'>{$_SESSION['message_info']}</p>";
                }

                unset($_SESSION["message_err"]);
                unset($_SESSION["message_info"]);
            ?>
            <p class="auth-hint">Ещё нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
        </form>
    </div>
    <footer>
        <p>© 2024 Государственный банк Эсептии</p>
        <a class="mailto" href="mailto:statebank@eseptia.site">statebank@eseptia.site</a>
    </footer>
</body>
</html>