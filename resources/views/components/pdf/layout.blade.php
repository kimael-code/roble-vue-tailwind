<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 2cm;
        }

        header {
            position: fixed;
            left: 0px;
            right: 0px;
            height: 100px;
            margin-top: -60px;
        }

        footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            height: 70px;
            margin-bottom: -70px;
        }
    </style>
</head>

<body>
    <header>
        <table border="0">
            <tr rowspan="3">
                <img src="{{ $logo }}" alt="Logo">
            </tr>
            <tr>
                <th>{{ $title }}</th>
                <th>{{ $appName }}</th>
                <td align="right">Generado a la fecha: {{ $timestamp }}</td>
            </tr>
        </table>
    </header>
    <footer></footer>

    <main>
        {{ $slot }}
    </main>
</body>

</html>