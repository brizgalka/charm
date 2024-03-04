<?php

    session_start();
    require_once 'connect.php';

    if (!isset($_SESSION['user']['transaction']['receiver_id']) && !isset($_SESSION['user']['transaction']['receiver_email'])) {
        $_SESSION['message_err'] = 'Укажите данные получателя';
        header('Location: ../profile.php');
        exit;
    }

    if(!isset($_POST['amount'])) {
        $_SESSION['message_err'] = 'Сумма не указана';
        header('Location: ../transfer.php');
        exit;
    }

    $amount = intval($_POST['amount']);

    if($amount < 1) {
        $_SESSION['message_err'] = "Сумма перевода должна быть больше 1 мн.";
        header('Location: ../transfer.php');
        exit;
    }

    $method = $_SESSION['user']['transaction']['method'];

    $stmt = $connect->prepare("SELECT * FROM users WHERE $method = ?");

    if ($method === 'id') {
        $receiver_id = $_SESSION['user']['transaction']['receiver_id'];
        $stmt->bind_param('i', $receiver_id);
    } else {
        $receiver_email = $_SESSION['user']['transaction']['receiver_email'];
        $stmt->bind_param('s', $receiver_email);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if(mysqli_num_rows($result) < 1) {
        $_SESSION['message_err'] = 'Пользователь не найден';
        header('Location: ../profile.php');
        exit;
    }

    $receiver_balance = $result->fetch_assoc()["balance"];
    
    if ($method === 'id') {
        $stmt->bind_param('i', $_SESSION["user"]["id"]);
    } else {
        $stmt->bind_param('s', $_SESSION["user"]["email"]);
    }
    $stmt->execute();
    $sender_id = $stmt->get_result()->fetch_assoc()["id"];
    $stmt->execute();
    $sender_balance = $stmt->get_result()->fetch_assoc()["balance"];

    if ($sender_balance < $amount) {
        $_SESSION['message_err'] = 'Недостаточно средств';
        header('Location: ../transfer.php');
        exit;
    }

    $new_receiver_balance = $receiver_balance + $amount;
    $new_sender_balance = $sender_balance - $amount;



    $connect->begin_transaction();

    try {
        $stmt = $connect->prepare("UPDATE users SET balance = ? WHERE $method = ?");
        if ($method === 'id') {
            $stmt->bind_param('ii', $new_receiver_balance, $receiver_id);
        } else {
            $stmt->bind_param('is', $new_receiver_balance, $receiver_email);
        }

        $stmt->execute();

        if ($method === 'id') {
            $stmt->bind_param('ii', $new_sender_balance, $_SESSION["user"]["id"]);
        } else {
            $stmt->bind_param('is', $new_sender_balance, $_SESSION["user"]["email"]);
        }

        $stmt->execute();

        $stmt = $connect->prepare("INSERT INTO transactions(sender_id, receiver_id, amount, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiis', $sender_id, $receiver_id, $amount, $_POST["comment"]);
        $stmt->execute();

        $connect->commit();
    } catch (mysqli_sql_exception $e) {
        $connect->rollback();

        unset($_SESSION['user']['transaction']);
        $_SESSION['message_err'] = 'Произошла ошибка при создании записи<br>Попробуйте ещё раз позднее';
        header('Location: ../profile.php');
        exit;
    }

    unset($_SESSION['user']['transaction']);
    $_SESSION['message_info'] = 'Перевод прошёл успешно';
    header('Location: ../profile.php');