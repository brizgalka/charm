<!-- Types: 1 (personal), 2 (shared) -->
<!-- personal accounts may only have one owner, while shared ones can have up to 8 owners -->
<!-- Currencies: 1 (Eseptian mona), 2 (Руснявый ruble)-->
<!-- Moved to TODO (вообще похуй, я лучше сначала оплату запилю) -->

<?php
    session_start();

    require_once 'connect.php';

    $account_name = $_POST["name"];
    $account_type = $_POST["type"];
    $account_currency = $_POST["currency"]; 
    $account_owners = json_encode(array($_SESSION["user"]["id"]));

    $stmt = $connect->prepare('INSERT INTO accounts(name, type, currency, owners) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('siis', $account_name, $account_type, $account_currency, $account_owners);
    $stmt->execute();

    $_SESSION['message_info'] = "Счёт успешно создан";
    header("Location: ../profile.php");