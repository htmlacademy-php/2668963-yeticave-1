<?php
    use Symfony\Component\Mailer\Transport;
    use Symfony\Component\Mailer\Mailer;
    use Symfony\Component\Mime\Email;

    $winMaxBets = getExpirationLotsMaxBetList($link);

    foreach ($winMaxBets as $winMaxBet) {
        $winnerId = $winMaxBet["betUserID"];
        $userEmail = $winMaxBet["email"];
        $userName = $winMaxBet["name"];
        
        $lotId = $winMaxBet['lotId'];
        $lotTitle = $winMaxBet['title'];

        updateWinner($link, $lotId, $winnerId);

        // Конфигурация траспорта
        $dsn = 'smtp://*EMAIL*:*PASSWORD*@smtp.gmail.com:587';
        $transport = Transport::fromDsn($dsn);


        $messageText = include 'email.php';

        // Формирование сообщения
        $message = new Email();
        $message->to($userEmail);
        $message->from($userEmail);
        $message->subject("Ваша ставка победила");
        $message->html($messageText);

        // Отправка сообщения
        $mailer = new Mailer($transport);
        $mailer->send($message);
    }
