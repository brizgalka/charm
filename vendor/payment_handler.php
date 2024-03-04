<?php
    session_start();

    require_once 'connect.php';

    if(!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }

    $user_id = $_SESSION['user']['id'];
    $payment_id = $_SESSION['user']['payment']['id'];

    // Fetch payment data
    $stmt = $connect->prepare('SELECT * FROM `payments` WHERE `id` = ?');
    $stmt->bind_param('i', $payment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $payment_data = $result->fetch_assoc();

    // Fetch current user (sender) data
    $stmt = $connect->prepare('SELECT * FROM `users` WHERE `id` = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();

    // Fetch reciever data
    $stmt->bind_param('i', $payment_data['receiver_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $receiver_data = $result->fetch_assoc();

    if ($user_data['balance'] < $payment_data['amount']) {
        unset($_SESSION['user']['payment']);
        $_SESSION['message_err'] = 'Недостаточно средств';
        
        header("Location: ../payment.php?id={$payment_id}");
        exit;
    }

    $new_receiver_balance = $receiver_data['balance'] + $payment_data['amount'];
    $new_sender_balance = $user_data['balance'] - $payment_data['amount'];

    $connect->begin_transaction();

    try {
        $stmt = $connect->prepare("UPDATE users SET balance = ? WHERE id = ?");

        $stmt->bind_param('ii', $new_receiver_balance, $receiver_data['id']);
        $stmt->execute();

        $stmt->bind_param('ii', $new_sender_balance, $user_id);
        $stmt->execute();

        $stmt = $connect->prepare("INSERT INTO transactions(sender_id, receiver_id, amount) VALUES (?, ?, ?)");
        $stmt->bind_param('iii', $user_id, $receiver_data['id'], $payment_data['amount']);
        $stmt->execute();

        $stmt = $connect->prepare("UPDATE payments SET status = 1 WHERE id = ?");
        $stmt->bind_param('i', $payment_id);
        $stmt->execute();

        $connect->commit();
    } catch (mysqli_sql_exception $e) {
        $connect->rollback();

        unset($_SESSION['user']['payment']);
        $_SESSION['message_err'] = 'Произошла ошибка при создании записи<br>Попробуйте ещё раз позднее';
        header("Location: ../payment.php?id={$payment_id}");
        exit;
    }

    unset($_SESSION['user']['payment']);
    $_SESSION['message_info'] = 'Оплата прошла успешно';
    header('Location: ../profile.php');