<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SHARKLINK')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-page font-sans text-ink antialiased">
    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <main class="flex min-w-0 flex-1 flex-col gap-6 px-4 pb-10 sm:px-10">
            @include('partials.topbar')
            @yield('content')
        </main>
    </div>
</body>
</html>
