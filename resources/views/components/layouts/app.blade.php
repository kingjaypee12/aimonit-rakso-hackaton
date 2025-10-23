<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- ... other head elements ... -->
        <title>{{ env('APP_URL') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-100 flex flex-col items-center justify-center">
        <!-- ✅ Responsive container -->
        <div class="w-screen max-w-screen bg-white shadow-lg overflow-hidden">
            {{ $slot ?? '' }}
        </div>

        @livewireScripts
    </body>
</html>
