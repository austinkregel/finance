<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body v-cloak>
<div id="app" :class="{ 'bg-gray-800 text-white': $store.getters.darkMode, 'bg-gray-200 text-gray-800': !$store.getters.darkMode }">
        @include('layouts.nav')

        <main>
            @yield('content')
        </main>
    </div>
    @include('darkmode')
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
