<x-admin-layout title="Pacientes" :breadcrumbs="[
    [
        'name' => 'Dashboard', 
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Pacientes', 
        'href' => route('admin.patients.index')
    ],
    [
        'name' => 'Nuevo', 
    ]
]">

</x-admin-layout>
