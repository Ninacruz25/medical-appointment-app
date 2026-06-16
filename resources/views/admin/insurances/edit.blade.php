<x-admin-layout title="Editar Seguro" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Seguros y Convenios',
        'href' => route('admin.insurances.index')
    ],
    [
        'name' => 'Editar Seguro',
    ]
]">

<x-wire-card>
    <x-validation-errors class="mb-4"/>
    <form action="{{ route('admin.insurances.update', $insurance) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div class="grid lg:grid-cols-2 gap-4">
                <x-wire-input
                    label="Nombre *"
                    name="name"
                    placeholder="Nombre del seguro o aseguradora"
                    required
                    :value="old('name', $insurance->name)">
                </x-wire-input>

                <x-wire-input
                    label="Código de convenio"
                    name="code"
                    placeholder="Ej. CONV-102"
                    :value="old('code', $insurance->code)">
                </x-wire-input>

                <x-wire-input
                    label="Teléfono de contacto"
                    name="phone"
                    placeholder="Ej. 9999999999"
                    inputmode="tel"
                    :value="old('phone', $insurance->phone)">
                </x-wire-input>

                <x-wire-input
                    label="Correo electrónico de contacto"
                    name="email"
                    type="email"
                    placeholder="Ej. contacto@seguro.com"
                    :value="old('email', $insurance->email)">
                </x-wire-input>
            </div>

            <div>
                <x-wire-checkbox
                    label="Seguro activo"
                    name="status"
                    value="1"
                    id="insurance_status"
                    :checked="old('status', $insurance->status)">
                </x-wire-checkbox>
                <p class="text-sm text-gray-500 mt-1">
                    Indica si este seguro está disponible para asignarse a pacientes.
                </p>
            </div>

            <div class="flex justify-end space-x-2">
                <x-wire-button outline gray href="{{ route('admin.insurances.index') }}">
                    Cancelar
                </x-wire-button>
                <x-wire-button type="submit" blue>
                    Actualizar Seguro
                </x-wire-button>
            </div>
        </div>
    </form>
</x-wire-card>

</x-admin-layout>
