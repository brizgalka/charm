<?php
    session_start();

    if(!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }

    # Check if transfer.php is refering to itself and set all the data from the session if it does
    if (!isset($_POST['receiver_id']) && !isset($_POST['receiver_email'])) {
        if(!isset($_SESSION['user']['transaction']['receiver_id']) && !isset($_SESSION['user']['transaction']['receiver_email'])) {
            $_SESSION['message_err'] = 'Укажите данные получателя';
            header('Location: profile.php');
            exit;
        } else {
            if(isset($_SESSION['user']['transaction']['receiver_email'])) {
                $receiver_email = $_SESSION['user']['transaction']['receiver_email'];
            } else {
                $receiver_id = $_SESSION['user']['transaction']['receiver_id'];
            }
            $method = $_SESSION['user']['transaction']['method'];
        }
    }

    # Set current transaction data in session for further handling
    if(isset($_POST['receiver_email'])) {
        $method = 'email';
        $receiver_email = $_POST['receiver_email'];
        $_SESSION['user']['transaction']['receiver_email'] = $receiver_email;
        $_SESSION['user']['transaction']['method'] = $method;
    } 
    if(isset($_POST['receiver_id'])) {
        $method = 'id';
        $receiver_id = $_POST['receiver_id'];
        $_SESSION['user']['transaction']['receiver_id'] = $receiver_id;
        $_SESSION['user']['transaction']['method'] = $method;
    }

    # ID validation
    if($method == 'id') {

        if (!filter_var($receiver_id, FILTER_VALIDATE_INT)) {
            unset($_SESSION['user']['transaction']);
            $_SESSION['message_err'] = 'ID обязан быть числом' . $receiver_id . '...';
            header('Location: profile.php');
            exit;
        } 

        $receiver_id = intval($receiver_id);

        # Доебаться до user if they EVER try to transfer money to themself
        if($receiver_id === $_SESSION["user"]["id"]) {
            unset($_SESSION['user']['transaction']);
            $_SESSION['message_err'] = 'Чё, сука, самый умный?';
            header('Location: profile.php');
            exit;
        } 
    }

    # Email validation
    if($method == 'email') {

        if (!filter_var($receiver_email, FILTER_VALIDATE_EMAIL)) {
            unset($_SESSION['user']['transaction']);
            $_SESSION['message_err'] = 'Неверный Email';
            header('Location: profile.php');
            exit;
        }

        if($receiver_email === $_SESSION["user"]["email"]) {
            unset($_SESSION['user']['transaction']);
            $_SESSION['message_err'] = 'Чё, сука, самый умный?';
            header('Location: profile.php');
            exit;
        } 
    }

    require_once "vendor/config.php";
    require_once "vendor/connect.php";

    # Fetch receiver data
    $stmt = $connect->prepare("SELECT * FROM users WHERE $method = ?");

    if ($method === 'id') {
        $stmt->bind_param('i', $receiver_id);
    } else {
        $stmt->bind_param('s', $receiver_email);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if(mysqli_num_rows($result) < 1) {
        $_SESSION['message_err'] = 'Пользователь не найден';
        header('Location: profile.php');
        exit;
    }

    $receiver = mysqli_fetch_assoc($result);
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
    <title>Перевод – Charm</title>
</head>
<body>
    <div class="center-container column">
        <div class="profile-container">
            <div class="form-logo">
                <img src="assets/public/logo.svg" width=96>
                <h1>CHARM</h1>
                <h3>Перевод</h3>
            </div>

            <div>
                <div class="profile-info">
                    <div class="profile-name">
                        <img src="assets/public/user.svg" height=48>
                        <div class="profile-details">
                            <p><?= $receiver["first_name"] ?> <?= $receiver["middle_name"] ?> <?= $receiver["last_name"] ?></p>
                            <p class="profile-email"><?= $receiver["email"]?></p>
                        </div>
                    </div>
                </div>
                <div class="transaction-info">
                    <form class="transfer-form" action="vendor/transfer.php" method="post">
                            <input type="number" name="amount" min="1" placeholder="Сумма" required>
                            <textarea type="text" name="comment" placeholder="Комментарий к переводу (до 150 символов)" maxlength="150" rows="5"></textarea>
                            <button type="submit">Перевести</button>
                    </form>
                </div>

                <button class="back-btn" onclick="location.href='profile.php'" type="button">« Вернуться в профиль</button>

                <?php
                    if(isset($_SESSION["message_err"])) {
                        echo "<p class='message_err'>{$_SESSION['message_err']}</p>";
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