<!doctype html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Zapytanie {{ $data->id_zapyt }}</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #4f46e5;
            font-size: 24px;
        }
        .wznowienie-badge {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f9fafb;
            color: #6b7280;
            width: 30%;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .section-title {
            background-color: #4f46e5;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 50px;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .description {
            white-space: pre-wrap;
            background-color: #fdfdfd;
            padding: 15px;
            border: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        @if ($data->wznowienie === 2)
            <div class="wznowienie-badge">WZNOWIENIE</div>
        @endif
        <h1>ZAPYTANIE NR {{ $data->id_zapyt }}</h1>
        <p>Data wygenerowania: {{ date('d.m.Y H:i') }}</p>
    </div>

    <div class="section-title">INFORMACJE OGÓLNE</div>
    <table>
        <tr>
            <th>Klient</th>
            <td><strong>{{ $data->client->nazwa }}</strong></td>
        </tr>
        <tr>
            <th>Nazwa projektu</th>
            <td><strong>{{ $data->nazwa_projektu }}</strong></td>
        </tr>
        <tr>
            <th>Lokalizacja</th>
            <td>{{ $data->miejscowosc }}, {{ $data->kraj->name }}</td>
        </tr>
        <tr>
            <th>Zakres</th>
            <td>{{ $data->zakres->name }}</td>
        </tr>
    </table>

    <div class="section-title">TERMINY I OSOBY</div>
    <table>
        <tr>
            <th>Data otrzymania</th>
            <td>{{ $data->data_otrzymania }}</td>
        </tr>
        <tr>
            <th>Planowany termin złożenia</th>
            <td>{{ $data->data_zlozenia }}</td>
        </tr>
        <tr>
            <th>Opracowuje</th>
            <td>{{ $data->opracowuje->first_name ?? '' }} {{ $data->opracowuje->last_name ?? '' }}</td>
        </tr>
        <tr>
            <th>Realizacja</th>
            <td>Od: {{ $data->start ?? '-' }} Do: {{ $data->end ?? '-' }}</td>
        </tr>
    </table>

    <div class="section-title">FINANSE</div>
    <table>
        <tr>
            <th>Kwota szacowana</th>
            <td><strong>{{ number_format($data->kwota, 2, ',', ' ') }} {{ $data->waluta->name ?? '' }}</strong></td>
        </tr>
    </table>

    <div class="section-title">OPIS PROJEKTU</div>
    <div class="description">
        {{ $data->opis }}
    </div>

    <div class="footer">
        MKL CRM - System Zarządzania Zapytaniami | Wygenerowano automatycznie
    </div>
</body>
</html>
