<x-admin-layout title="Usuarios" :breadcrumbs="[
    [
        'name' => 'Dashboard', 
        'href' => route('admin.dashboard')
    ],
    [
        'name' => 'Usuarios', 
        'href' => route('admin.users.index')
    ],
    [
        'name' => 'Crear usuario', 
    ]
]">

<x-wire-card>
    <x-validation-errors class="mb-4"/>
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div class="grid lg:grid-cols-2 gap-4">
                <x-wire-input
                    label="Nombre" 
                    name="name" 
                    placeholder="Nombre del usuario" 
                    required
                    :value="old('name', $user->name)">
                </x-wire-input>
                <x-wire-input
                    label="Correo electrónico" 
                    name="email" 
                    type="email"
                    placeholder="Correo electrónico del usuario" 
                    autocomplete="email"
                    required
                    :value="old('email', $user->email)">
                </x-wire-input>
                <x-wire-input
                    label="Contraseña" 
                    name="password" 
                    type="password"
                    placeholder="Mínimo 8 caracteres" 
                    autocomplete="new-password">
                </x-wire-input>
                <x-wire-input
                    label="Confirmar contraseña" 
                    name="password_confirmation" 
                    type="password"
                    placeholder="Repita contraseña" 
                    autocomplete="new-password">
                </x-wire-input>
                <x-wire-input
                    label="Número ID" 
                    name="id_number" 
                    placeholder="Ej. 123456789"
                    autocomplete="off"
                    inputmode="numeric"
                    required
                    :value="old('id_number', $user->id_number)">
                </x-wire-input>
                <x-wire-input
                    label="Teléfono" 
                    name="phone" 
                    placeholder="Ej. 9999999999"
                    autocomplete="tel"
                    inputmode="tel"
                    required
                    :value="old('phone', $user->phone)">
                </x-wire-input>
            </div>
            <x-wire-input
                name="address"
                label="Dirección"
                requiared
                placeholder="Ej. Calle 90 283"
                autocomplete="street-address"
                :value="old('address', $user->address)"
            ></x-wire-input>

            <div class="space-y-1">
                <x-wire-native-select
                    name="role_id"
                    label="Roles"
                    required
                >
                    <option value="">
                        Seleccione un rol
                    </option>
                    
                    @foreach ($roles as $role)
                        <option 
                            value="{{ $role->id }}"
                            @selected(old('role_id', $user->roles->first()->id) == $role->id)
                        >{{ $role->name }}</option>
                    @endforeach
                </x-wire-native-select>
                <p class="text-sm text-gray-500">
                    Define los permisos y accesos del usuario.
                </p>
            </div>

            <div class="flex justify-end">
                <x-wire-button type="submit" blue>
                    Actualizar
                </x-wire-button>
            </div>
        </div>
    </form>
</x-wire-card>

</x-admin-layout>
