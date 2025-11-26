<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Global SweetAlert Functions -->
        <script>
            // Confirmation for Create actions
            function confirmCreate(form, title = 'Create New Record', text = 'Are you sure you want to create this record?') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Yes, create it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            // Confirmation for Update actions
            function confirmUpdate(form, title = 'Update Record', text = 'Are you sure you want to update this record?') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3B82F6',
                    cancelButtonColor: '#EF4444',
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            // Confirmation for Delete actions
            function confirmDelete(form, title = 'Delete Record', text = 'This action cannot be undone!') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            // Success message after actions
            function showSuccess(title = 'Success!', text = 'Operation completed successfully') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'success',
                    confirmButtonColor: '#10B981'
                });
            }

            // Error message
            function showError(title = 'Error!', text = 'Something went wrong') {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'error',
                    confirmButtonColor: '#EF4444'
                });
            }

            // Global logout confirmation function
            function confirmLogout() {
                Swal.fire({
                    title: 'Sign Out',
                    text: 'Are you sure you want to sign out?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Yes, sign out',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            }

            // Auto-show SweetAlert based on session messages
            document.addEventListener('DOMContentLoaded', function() {
                @if(session('success'))
                    Swal.fire({
                        title: 'Success!',
                        text: '{{ session('success') }}',
                        icon: 'success',
                        confirmButtonColor: '#10B981',
                        timer: 3000,
                        showConfirmButton: true
                    });
                @endif

                @if(session('error'))
                    Swal.fire({
                        title: 'Error!',
                        text: '{{ session('error') }}',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                @endif

                @if(session('warning'))
                    Swal.fire({
                        title: 'Warning!',
                        text: '{{ session('warning') }}',
                        icon: 'warning',
                        confirmButtonColor: '#F59E0B'
                    });
                @endif

                @if(session('info'))
                    Swal.fire({
                        title: 'Information',
                        text: '{{ session('info') }}',
                        icon: 'info',
                        confirmButtonColor: '#3B82F6'
                    });
                @endif
            });
        </script>
    </body>
</html>
