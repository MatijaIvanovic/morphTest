<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        @vite(['resources/js/styles/app.css'])
        <link rel="icon" href="{{ url('favicon.ico') }}" />

    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div id='app'></div>
        @vite(['resources/js/app.js'])
    </body>
</html>
