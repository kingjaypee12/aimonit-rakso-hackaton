<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- ... other head elements ... -->
        <title>{{ env('APP_URL') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.tailwindcss.com"></script>
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-100 flex flex-col items-center justify-center">
        <!-- ✅ Responsive container -->
        <div class="w-screen max-w-screen bg-white shadow-lg overflow-hidden">
            {{ $slot ?? '' }}
            @yield('content')
        </div>

        @livewireScripts
    </body>
</html>
