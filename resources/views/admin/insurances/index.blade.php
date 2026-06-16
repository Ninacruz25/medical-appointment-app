<x-admin-layout title="Seguros y Convenios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Seguros y Convenios',
    ]
]">

    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.insurances.create') }}">
            <i class="fa-solid fa-plus me-1"></i>
            Nuevo Seguro
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.insurance-table')

</x-admin-layout>
