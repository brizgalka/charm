<?php
    session_start();

    if(!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }

    unset($_SESSION["user"]["transaction"]);

    require_once "vendor/config.php";
    require_once "vendor/connect.php";

    $stmt = $connect->prepare('SELECT * FROM `users` WHERE `id` = ?');
    $stmt->bind_param('s', $_SESSION["user"]["id"]);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
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
    <title>Профиль – Charm</title>
</head>
<body>
    <div class="center-container column">
        <div class="profile-container">
            <div class="form-logo">
                <img src="assets/public/logo.svg" width=96>
                <h1>CHARM</h1>
                <h3>Профиль</h3>
            </div>

            <div>
                <div class="profile-info">
                    <div class="profile-name">
                        <img src="assets/public/user.svg" height=48>
                        <div class="profile-details">
                            <p><?= $data["first_name"] ?> <?= $data["middle_name"] ?> <?= $data["last_name"] ?></p>
                            <p class="profile-email"><?= $_SESSION["user"]["email"]?></p>
                        </div>
                    </div>
                    <p>Ваш баланс: <?= $data["balance"] ?> <?= $currency_sign ?></p>
                    <p>Ваш ID: <?= $_SESSION["user"]["id"] ?></p>
                </div>
                <div class="profile-transfer">
                    <h3 class="title">Переводы</h3>
                    <form class="profile-transfer-form" action="transfer.php" method="post">
                        <div>
                            <input type="number" name="receiver_id" placeholder="ID получателя" required>
                            <!-- Add currency_sign -->
                        </div>
                        <button type="submit">Перевести</button>
                    </form>
                    <form class="profile-transfer-form last-element" action="transfer.php" method="post">
                        <div>
                            <input type="email" name="receiver_email" placeholder="Email получателя" required>
                        </div>
                        <button type="submit">Перевести</button>
                    </form>
                </div>

                <?php
                    if(isset($_SESSION["message_err"])) {
                        echo "<p class='message_err'>{$_SESSION['message_err']}</p>";
                    } else if (isset($_SESSION["message_info"])) {
                        echo "<p class='message_info'>{$_SESSION['message_info']}</p>";
                    }

                    unset($_SESSION["message_err"]);
                    unset($_SESSION["message_info"]);
                ?>

                <button class="history-btn" onclick="location.href='history.php'" type="button">История переводов</button>
                <button class="logout-btn" onclick="location.href='vendor/logout.php'" type="button">Выйти</button>
            </div>
        </div>
    </div>
    <footer>
        <p>© 2024 Государственный банк Эсептии</p>
        <a class="mailto" href="mailto:statebank@eseptia.site">statebank@eseptia.site</a>
    </footer>
</body>
</html>