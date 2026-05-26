<x-admin-layout title="Doctores" :breadcrumbs="[
    [
        'name' => 'Dashboard', 
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Doctores', 
        'href' => route('admin.doctors.index')
    ],
    [
        'name' => 'Editar', 
    ]
]">

<form action="{{route('admin.doctors.update', $doctor->id)}}" method="POST">
    @csrf
    @method('PUT')
    {{-- Encabezado con foto y acciones --}}
    <x-wire-card class="mb-8">
        <div class="lg:flex lg:justify-between lg:items-center">
            <div class="flex items-center">
                <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}"
                class="h-20 w-20 rounded-full object-cover object-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 ml-4">{{ $doctor->user->name }}</h2>
                </div>
            </div>
            <div class="flex space-x-3 mt6 lg:mt-0">
                <x-wire-button outline gray href="{{route('admin.doctors.index')}}">
                    Volver
                </x-wire-button>

                <x-wire-button type="submit">
                    <i class="fa-solid fa-check"></i>
                    Guardar cambios
                </x-wire-button>
        </div>
    </x-wire-card>

    <x-wire-card>
        <div class="col-span-1 md:col-span-2 gap-4 mt-4">
            <x-wire-native-select
                name="specialty_id"
                label="Especialidad"
                required
            >
                <option value="">Selecciona una especialidad</option>

                @foreach ($specialties as $specialty)
                    <option
                        value="{{ $specialty->id }}"
                        @selected(old('specialty_id', $doctor->specialty_id) == $specialty->id)
                    >{{ $specialty->name }}</option>
                @endforeach

            </x-wire-native-select>
        </div>

            <div class="col-span-1 md:col-span-2 gap-4 mt-4">
                <x-wire-input
                    name="medical_license_number"
                    label="Número de licencia médica"
                    placeholder="Ej. 12345678"
                    value="{{ old('medical_license_number', $doctor->medical_license_number) }}" />
            </div>

            <div class="col-span-2 md:col-span-2 gap-4 mt-4">
                <x-wire-input
                    name="biography"
                    label="Biografía"
                    value="{{ old('biography', $doctor->biography) }}" />
            </div>

        </div>
    </x-wire-card>

</form>

</x-admin-layout>
