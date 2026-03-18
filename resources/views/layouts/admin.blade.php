@props([
    'title' => config('app.name', 'Laravel'),
    'breadcrumbs' => [],
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://kit.fontawesome.com/6d092cdef2.js" crossorigin="anonymous"></script>

        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <wireui:scripts />

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50">
    
        @include('layouts.includes.admin.navigation')

        @include('layouts.includes.admin.sidebar')

        <div class="p-4 sm:ml-64 mt-14">

            <div class="mt-14 flex items-center justify-between w-full">
                @include('layouts.includes.admin.breadcrumb')
                @isset($action)
                    <div>
                        {{ $action }}
                    </div>
                @endisset
            </div>
            {{ $slot }}
        
        </div>


        @stack('modals')

        {{-- Mostrar Sweet alert --}}
        @if(session('swal'))
            <script>
                Swal.fire(@json(session('swal')));
            </script>
        @endif

        @livewireScripts

        <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>

        <script>
            //Busca todos los elementos para borrar
            const forms = document.querySelectorAll('.delete-form');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    event.preventDefault();

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás revertir esto!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, eliminarlo!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>

    </body>
</html>
