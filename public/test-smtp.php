<?php

echo "<h1>Szybki Test SMTP v5</h1>";

$host = "serwer1551306.home.pl";
$user = "crmsystem@mkl.pl";
$pass = "6wp0o8092792P7BXw9kWs";

// Testujemy oba porty z krótkim timeoutem
$ports = [465 => 'ssl', 587 => 'tls'];

foreach ($ports as $port => $encryption) {
    echo "<h2>Test portu $port ($encryption)</h2>";

    $timeout = 5;
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);

    $remote = ($port == 465) ? "ssl://$host" : "tcp://$host";

    $start = microtime(true);
    $socket = @stream_socket_client($remote . ":" . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
    $end = microtime(true);

    if (!$socket) {
        echo "<p style='color: red;'>BŁĄD POŁĄCZENIA: $errstr ($errno) - czas: " . round($end - $start, 2) . "s</p>";
    } else {
        echo "<p style='color: green;'>POŁĄCZONO z socketem w " . round($end - $start, 2) . "s</p>";
        echo "Odpowiedź serwera: " . fgets($socket, 1024) . "<br>";

        if ($port == 587) {
            fwrite($socket, "EHLO test.mkl.pl\r\n");
            echo "EHLO: " . fgets($socket, 1024) . "<br>";
            fwrite($socket, "STARTTLS\r\n");
            echo "STARTTLS: " . fgets($socket, 1024) . "<br>";
        }

        fclose($socket);
    }
}

echo "<hr><p>Jeśli port 587 pokazał 'STARTTLS: 220 Ready to start TLS', to użyj portu 587 i szyfrowania tls w .env</p>";
