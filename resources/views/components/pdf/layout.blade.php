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

        h4 {
            margin: 0;
        }

        .w-full {
            width: 100%;
        }

        .w-half {
            width: 50%;
        }
        .w-20 {
            width: 20%;
        }

        .margin-top {
            margin-top: 1.25rem;
        }

        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(241 245 249);
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table.products {
            font-size: 0.875rem;
        }

        table.products tr {
            background-color: rgb(96 165 250);
        }

        table.products th {
            color: #ffffff;
            padding: 0.5rem;
        }

        table tr.items {
            background-color: rgb(241 245 249);
        }

        table tr.items td {
            padding: 0.5rem;
        }

        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>

<body>
    <header>
        <table class="w-full" border="1">
            <tr>
                <td class="w-20">
                    <img src="{{ $logo }}" alt="Logo" width="200" />
                </td>
                <td>
                    <h2>{{ $title }}</h2>
                    <h3>{{ $appName }}</h3>
                    <pre>Generado a la fecha: {{ $timestamp }}</pre>
                </td>
            </tr>
        </table>
    </header>
    <footer></footer>

    <main>
        {{ $slot }}
    </main>
</body>

</html>