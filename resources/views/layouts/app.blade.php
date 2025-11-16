<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 flex flex-col justify-between">
            
            <div>
                @include('layouts.navigation')

                @if (isset($header))
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main>
                    {{ $slot }}
                </main>
            </div>

            <footer class="py-6 text-center text-xs text-gray-400 border-t border-gray-200 mt-10">
                <p class="mb-1">
                    <strong>Tecnolabs Sistema Comercial</strong> &copy; {{ date('Y') }}
                </p>
                <p>
                    Versão <strong>{{ config('app.version') }}</strong> 
                    <span class="mx-1">•</span>
                    Desenvolvido por <strong>Tecnolabs - Agência Digital</strong>
                </p>
            </footer>

        </div>
    </body>
</html>