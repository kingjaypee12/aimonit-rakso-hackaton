<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- ... other head elements ... -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-gray-100 flex flex-col items-center justify-center p-4 sm:p-6 md:p-8">
        <!-- ✅ Responsive container -->
        <div class="w-screen max-w-screen h-screen bg-white rounded-2xl shadow-lg overflow-hidden">
            {{ $slot ?? '' }}
        </div>
    
        @livewireScripts
    </body>
</html>