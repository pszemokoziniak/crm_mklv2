<!DOCTYPE html>
<html class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

{{--    @routes --}}{{-- TA LINIA JEST KLUCZOWA DLA ZIGGY --}}

    <script src="{{ mix('/js/app.js') }}" defer></script>
    <title>MKL CRM</title>
    @inertiaHead
</head>
<body class="font-sans leading-none text-gray-700 antialiased">
    @inertia
</body>
</html>
