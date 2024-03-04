<!-- Statuses: 0 (Created), 1 (Paid) -->

<?php
    session_start();

    require_once 'connect.php';

    $receiver_id = $_SESSION['user']['id'];
    $amount = $_POST['amount'];

    $stmt = $connect->prepare('INSERT INTO payments(receiver_id, amount) VALUES (?, ?)');
    $stmt->bind_param('ii', $receiver_id, $amount);
    $stmt->execute();

    $id = mysqli_insert_id($connect);

    $_SESSION['message_info'] = "Запрос на оплату успешно создан!<br>https://charm.eseptia.site/payment.php?id={$id}";
    header('Location: ../profile.php');