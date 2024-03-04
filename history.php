<?php
    session_start();

    require_once 'vendor/config.php';
    require_once 'vendor/connect.php';

    $stmt = $connect->prepare('SELECT * FROM `transactions` WHERE `sender_id` = ? OR `receiver_id` = ?');
    $stmt->bind_param('ss', $_SESSION['user']['id'], $_SESSION['user']['id']);
    $stmt->execute();

    $result = $stmt->get_result();
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
    <title>История – Charm</title>
</head>
<body>
    <div class="center-container column">
        <div class="profile-container">
            <div class="form-logo">
                <img src="assets/public/logo.svg" width=96>
                <h1>CHARM</h1>
                <h3>История переводов</h3>
            </div>

            <div class="transaction-list">
                <li>
                    <?php   
                        if (mysqli_num_rows($result) <= 0) {
                            echo '<ul> <h3 class="no-transactions-message">Хм...<br>Тут ничего нет.</h3> <ul>';
                        } else {
                            $row = $result->fetch_all($mode = MYSQLI_ASSOC);
                            $stmt = $connect->prepare('SELECT * FROM `users` WHERE `id`= ?');
                            $transactions_count = mysqli_num_rows($result);

                            for($i = 0; $i < $transactions_count; $i++) {

                                if ($row[$i]['sender_id'] != $_SESSION['user']['id']) {
                                    $stmt->bind_param('s', $row[$i]['sender_id']);
                                    $method = "Получено";
                                } else {
                                    $stmt->bind_param('s', $row[$i]['receiver_id']);
                                    $method = "Отправлено";
                                }

                                $stmt->execute();

                                $result = $stmt->get_result();
                                $user = $result->fetch_assoc();

                                if ($row[$i]["comment"] != "") {
                                    $comment = "<p>Комментарий: {$row[$i]['comment']}<p>";
                                } else {
                                    $comment = '';
                                }

                                echo "<ul>
                                        <div class='transaction-info'>
                                            <div class='profile-info-transaction'>
                                                <div class='profile-name'>
                                                    <img src='assets/public/user.svg' height=48>
                                                    <div class='profile-details'>
                                                        <p>{$user["first_name"]} {$user["middle_name"]} {$user["last_name"]}</p>
                                                        <p class='profile-email'>agniya.kaneva@inbox.ru</p>
                                                    </div>
                                                </div>
                                                <p>{$method} {$row[$i]['amount']} {$currency_sign}</p>
                                                {$comment}
                                            </div>
                                        </div>
                                    </ul>"; 
                            }
                        }
                    ?>
                </li>
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

                <button class="back-btn" onclick="location.href='profile.php'" type="button">« Вернуться в профиль</button>
            </div>
        </div>
    </div>
    <footer>
        <p>© 2024 Государственный банк Эсептии</p>
        <a class="mailto" href="mailto:statebank@eseptia.site">statebank@eseptia.site</a>
    </footer>
</body>
</html>