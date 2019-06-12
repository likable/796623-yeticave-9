<?php

require_once("helpers.php");
require_once("database.php");
require_once("vendor/autoload.php");

//получение списка лотов с истёкшим сроком годности без победителей
$sql_expired_lots = "SELECT * FROM lots WHERE dt_end <= NOW() 
    AND winner_id IS NULL;";
$expired_lots_object = mysqli_query($database_connection, $sql_expired_lots);
$expired_lots = mysqli_fetch_all($expired_lots_object, MYSQLI_ASSOC);

foreach ($expired_lots as $expired_lot) {
    $lot_id = (int) $expired_lot["id"];
    
    //определение id пользователя сделавшего последнюю ставку
    $sql_win = "SELECT better_id, user_name, email, lot_name, lot_id "
            . "FROM bets b LEFT JOIN lots l ON lot_id = l.id "
            . "LEFT JOIN users u ON better_id = u.id "
            . "WHERE lot_id=? "
            . "ORDER BY dt_bet DESC";
    $stmt_win = mysqli_prepare($database_connection, $sql_win);
    mysqli_stmt_bind_param($stmt_win, 'i', $lot_id);
    mysqli_stmt_execute($stmt_win);
    $sql_win_result = mysqli_stmt_get_result($stmt_win);
    if ($sql_win_result) {
        $winner = mysqli_fetch_assoc($sql_win_result);
    }
    
    if ($winner) {
        //отправляю письмо победителю
        $content = include_template('email.php', ['winner' => $winner]);
        $to = $winner["email"];
        
        //Конфигурация транспорта
        $transport = new Swift_SmtpTransport("phpdemo.ru", 25);
        $transport->setUsername("keks@phpdemo.ru");
        $transport->setPassword("htmlacademy");
        
        //Формирование сообщения
        $message = new Swift_Message("Тема письма");
        $message->setTo([$to => 'Победителю']);
        $message->setBody($content);
        $message->setFrom("keks@phpdemo.ru");
        
        //Отправка сообщения
        $mailer = new Swift_Mailer($transport);
        $mailer->send($message);
        
        $winner_id = $winner["better_id"];
    }
    else {
        $winner_id = -1;
    }
    
    //добавляю победителя в таблицу lots
    $sql_add_winner = "UPDATE lots SET winner_id=? WHERE id=?;";
    $stmt_add_winner = mysqli_prepare($database_connection, $sql_add_winner);

    if ($stmt_add_winner === false) {
        $errorMsg = "STMT error: " . mysqli_error($database_connection);
        die($errorMsg);
    }

    mysqli_stmt_bind_param($stmt_add_winner, 'ii', $winner_id, $lot_id);
    mysqli_stmt_execute($stmt_add_winner);

    if (mysqli_errno($database_connection) > 0) {
        $errorMsg = 'Неувязочка: ' . mysqli_error($database_connection);
        die($errorMsg);
    } 
}