<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title')</title>
        <meta name="description" content="Test">
        @yield('meta')

        @stack('before-styles')
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        @stack('after-styles')
    </head>
    <body class="">
        <main role="main">
            @yield('content')
        </main>

        @stack('before-scripts')
        <script src="{{ asset('js/app.js') }}"></script>
        @stack('after-scripts')
    </body>
</html>
