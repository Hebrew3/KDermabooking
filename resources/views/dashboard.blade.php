<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'K-Derma') }} - Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Include Mobile Header -->
    <x-mobile-header />

    <!-- Include Sidebar based on user role -->
    @if(Auth::user()->role === 'admin')
        <x-admin-sidebar />
    @elseif(Auth::user()->role === 'staff')
        <x-staff-sidebar />
    @else
        <x-client-sidebar />
    @endif

    <!-- Main Content -->
    <div class="lg:ml-64 transition-all duration-300" id="main-content">
        <!-- Include Dashboard based on user role -->
        @if(Auth::user()->role === 'admin')
            @include('dashboards.admin')
        @elseif(Auth::user()->role === 'staff')
            @include('dashboards.staff')
        @else
            @include('dashboards.client')
        @endif
    </div>
</body>
</html>
