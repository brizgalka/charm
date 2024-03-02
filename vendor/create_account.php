<!-- Types: 1 (personal), 2 (shared) -->
<!-- personal accounts may only have one owner, while shared ones can have up to 8 owners -->
<!-- Currencies: 1 (Eseptian mona), 2 (Rusnjavyj ruble)-->

<?php 
    require_once 'connect.php';

    $_SESSION['message_info'] = "Счёт успешно создан"
    header("Location: ../profile.php");