<x-admin-layout title="Citas médicas" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas'],
]">
    <div class="flex justify-end mb-4">
        <x-wire-button href="{{ route('admin.appointments.create') }}" primary>
            <i class="fa-solid fa-plus me-1"></i> Nuevo
        </x-wire-button>
    </div>

    @livewire('admin.datatables.appointment-table')

</x-admin-layout>
