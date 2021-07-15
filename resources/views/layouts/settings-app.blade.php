<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>

    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <style>
        [v-cloak] {
            background: #2d3748;
        }
        [v-cloak] div {
            display: none;
        }
    </style>

    @yield('scripts', '')
</head>
<body>
    <div v-cloak id="app" :class="{ 'bg-gray-800 text-white': $store.getters.darkMode, 'bg-gray-200 text-gray-800': !$store.getters.darkMode }">
        @include('layouts.nav')
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <!-- Scripts -->
    @include('darkmode')

    <script src="{{ mix('js/settings-app.js') }}"></script>
</body>
</html>
