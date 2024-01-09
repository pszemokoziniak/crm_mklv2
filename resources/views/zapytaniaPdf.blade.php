<!doctype html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Zapytanie PDF</title>
</head>
<body style="font-family: DejaVu Sans">
    @foreach ($data as $item)
        <p>Zapytanie Id: {{$item->id_zapyt}}</p>
        <p>Otrzymał: {{$item->user->first_name}} {{$item->user->last_name}}</p>
        <p>Data otrzymania: {{$item->data_otrzymania}}</p>
        <p>Planowany termin złożenia: {{$item->data_zlozenia}}</p>
        <p>Klient: {{$item->client->nazwa}}</p>
        <p>Nazwa projektu: {{$item->nazwa_projektu}}</p>
        <p>Miejscowość: {{$item->miejscowosc}}</p>
        <p>Kraj: {{$item->kraj->name}}</p>
        <p>Zakres: {{$item->zakres->name}}</p>
        <p>Opracowuje: {{$item->user->first_name}} {{$item->user->last_name}}</p>
        <p>Planowany termin rozpoczęcia:: {{$item->start}}</p>
        <p>Planowany termin zakończenia:: {{$item->end}}</p>
        <p>Kwota: {{$item->kwota}}</p>
        <p>Waluta: {{$item->kraj->waluta}}</p>
        <p>Opis: {{$item->opis}}</p>
    @endforeach
</body>
</html>
