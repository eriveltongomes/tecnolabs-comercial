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
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-4">
                        
                        @if ($message = session()->pull('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <strong class="font-bold">Sucesso!</strong>
                                <span class="block sm:inline">{{ $message }}</span>
                            </div>
                        @endif

                        @if ($message = session()->pull('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <strong class="font-bold">Erro!</strong>
                                <span class="block sm:inline">{{ $message }}</span>
                            </div>
                        @endif
                        
                        @if ($message = session()->pull('info'))
                            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
                                <strong class="font-bold">Info:</strong>
                                <span class="block sm:inline">{{ $message }}</span>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative mb-4">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    {{ $slot }}
                </main>
            </div>

            <footer class="bg-white border-t border-gray-200 mt-10">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                        <div>
                            &copy; {{ date('Y') }} Tecnolabs - Sistema Comercial. Todos os direitos reservados.
                        </div>
                        <div class="flex items-center mt-2 md:mt-0 space-x-4">
                            <span>Versão {{ config('app.version') }}</span>
                            <span class="hidden md:inline">|</span>
                            <span>Desenvolvido por Tecnolabs</span>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </body>
</html>