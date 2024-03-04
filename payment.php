<?php
    session_start();

    if(!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }

    unset($_SESSION["user"]["payment"]);
    unset($_SESSION['user']['transaction']);

    require_once "vendor/config.php";
    require_once "vendor/connect.php";

    parse_str($_SERVER['QUERY_STRING'], $params);
    $payment_id = $params['id'];

    // Fetch payment data
    $stmt = $connect->prepare('SELECT * FROM `payments` WHERE `id` = ?');
    $stmt->bind_param('i', $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_data = $result->fetch_assoc();

    // Redirect user to their profile if the payment is already paid (1)
    if ($payment_data['status'] == 1) {
        header("Location: profile.php");
        exit;
    }

    // Redirect user to their profile if they try to transfer money to themself
    if($payment_data['receiver_id'] == $_SESSION['user']['id']) {
        header("Location: profile.php");
        exit;
    }

    // Fetch reciever data
    $stmt = $connect->prepare('SELECT * FROM `users` WHERE `id` = ?');
    $stmt->bind_param('i', $payment_data['receiver_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $receiver_data = $result->fetch_assoc();

    $_SESSION['user']['payment'] = $payment_data;
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
    <title>Оплата – Charm</title>
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
                            <p><?= $receiver_data["first_name"] ?> <?= $receiver_data["middle_name"] ?> <?= $receiver_data["last_name"] ?></p>
                            <p class="profile-email"><?= $_SESSION["user"]["email"]?></p>
                        </div>
                    </div>
                    <p>ID получателя: <?= $receiver_data['id'] ?></p>
                    <p>Запрошенная сумма: <?= $payment_data['amount'] ?> <?= $currency_sign ?></p>
                </div>
                <form class="profile-transfer-form" action="vendor/payment_handler.php" method="post">
                    <button type="submit">Оплатить</button>
                </form>

                <button class="back-btn" onclick="location.href='profile.php'" type="button">« Вернуться в профиль</button>

                <?php
                    if(isset($_SESSION["message_err"])) {
                        echo "<p class='message_err'>{$_SESSION['message_err']}</p>";
                    } else if (isset($_SESSION["message_info"])) {
                        echo "<p class='message_info'>{$_SESSION['message_info']}</p>";
                    }

                    unset($_SESSION["message_err"]);
                    unset($_SESSION["message_info"]);
                ?>
            </div>
        </div>
    </div>
    <footer>
        <p>© 2024 Государственный банк Эсептии</p>
        <a class="mailto" href="mailto:statebank@eseptia.site">statebank@eseptia.site</a>
    </footer>
</body>
</html>