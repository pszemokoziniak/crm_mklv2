<!doctype html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Zapytanie PDF</title>
</head>
    <body style="font-family: DejaVu Sans">
        <div style="border-style: dotted; border: #2d3748">
            @if ($data->wznowienie === 2 )
                <p><strong>Wznowienie</strong></p>
            @endif
            <p><strong>Zapytanie Id: {{$data->id_zapyt}}</strong></p>
            <p>Otrzymał: {{$data->user->first_name}} {{$data->user->last_name}}</p>
            <p>Data otrzymania: {{$data->data_otrzymania}}</p>
            <p>Planowany termin złożenia: {{$data->data_zlozenia}}</p>
            <p>Klient: <strong>{{$data->client->nazwa}}</strong></p>
            <p>Nazwa projektu: <strong>{{$data->nazwa_projektu}}</strong></p>
            <p>Miejscowość: {{$data->miejscowosc}}</p>
            <p>Kraj: {{$data->kraj->name}}</p>
            <p>Zakres: {{$data->zakres->name}}</p>
            <p>Opracowuje: {{$data->user->first_name}} {{$data->user->last_name}}</p>
            <p>Planowany termin rozpoczęcia:: <strong>{{$data->start}}</strong></p>
            <p>Planowany termin zakończenia:: <strong>{{$data->end}}</strong></p>
            <p>Kwota: <strong>{{$data->kwota}}</strong></p>
            <p>Waluta: {{$data->kraj->waluta}}</p>
            <p>Opis: {{$data->opis}}</p>
        </div>
    </body>
</html>
