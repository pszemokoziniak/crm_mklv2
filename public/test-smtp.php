<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

echo "<h1>Debugowanie Połączenia SMTP v4 (Raw Socket)</h1>";

$host = config('mail.mailers.smtp.host');
$port = config('mail.mailers.smtp.port');
$user = config('mail.mailers.smtp.username');
$pass = config('mail.mailers.smtp.password');

echo "Próba połączenia z $host na porcie $port...<br>";

// TEST 1: Ręczne sprawdzenie odpowiedzi serwera (bez SSL na starcie)
$socket = fsockopen($host, $port, $errno, $errstr, 10);
if (!$socket) {
    echo "BŁĄD SOCKETU: $errstr ($errno)<br>";
} else {
    echo "Socket otwarty. Odpowiedź serwera: " . fgets($socket, 1024) . "<br>";
    fwrite($socket, "EHLO test.mkl.pl\r\n");
    echo "EHLO sent. Odpowiedź: " . fgets($socket, 1024) . "<br>";
    fclose($socket);
}

echo "<br>Próba wysyłki przez Symfony Mailer z ignorowaniem SSL...<br>";

try {
    // Tworzymy transport ręcznie z wyłączoną weryfikacją
    $transport = new EsmtpTransport($host, $port, ($port == 465));
    $transport->setUsername($user);
    $transport->setPassword($pass);

    // To jest kluczowe dla serwerów z problematycznymi certyfikatami
    $transport->setStreamOptions([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);

    $mailer = new Mailer($transport);
    $email = (new Email())
        ->from(config('mail.from.address'))
        ->to('pszemo.koziniak@gmail.com')
        ->subject('Test CRM MKL v4 - ' . date('H:i:s'))
        ->text('Jeśli to widzisz, to znaczy że wyłączenie weryfikacji SSL pomogło.');

    $mailer->send($email);
    echo "<h2 style='color: green;'>SUKCES! Mail wysłany.</h2>";

} catch (\Exception $e) {
    echo "<h2 style='color: red;'>BŁĄD: " . $e->getMessage() . "</h2>";
    if (method_exists($e, 'getDebug')) {
        echo "<pre>" . $e->getDebug() . "</pre>";
    }
}
