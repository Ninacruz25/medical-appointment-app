<x-admin-layout title="Nueva cita" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas', 'href' => route('admin.appointments.index')],
    ['name' => 'Nuevo'],
]">

<form action="{{ route('admin.appointments.store') }}" method="POST">
    @csrf
    <x-wire-card>
        <div class="grid lg:grid-cols-2 gap-6">

            {{-- Paciente --}}
            <div>
                <x-wire-native-select label="Paciente *" name="patient_id">
                    <option value="">Selecciona un paciente</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                            {{ $patient->user->name }}
                        </option>
                    @endforeach
                </x-wire-native-select>
            </div>

            {{-- Doctor --}}
            <div>
                <x-wire-native-select label="Doctor *" name="doctor_id">
                    <option value="">Selecciona un doctor</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}" @selected(old('doctor_id') == $doctor->id)>
                            {{ $doctor->user->name }}
                        </option>
                    @endforeach
                </x-wire-native-select>
            </div>

            {{-- Fecha --}}
            <div>
                <x-wire-input
                    label="Fecha *"
                    name="date"
                    type="date"
                    value="{{ old('date') }}"
                    min="{{ now()->format('Y-m-d') }}"
                />
            </div>

            {{-- Duración (oculto, valor por defecto) --}}
            <input type="hidden" name="duration" value="15">

            {{-- Hora inicio --}}
            <div>
                <x-wire-input
                    label="Hora de inicio *"
                    name="start_time"
                    type="time"
                    value="{{ old('start_time') }}"
                />
            </div>

            {{-- Hora fin --}}
            <div>
                <x-wire-input
                    label="Hora de fin *"
                    name="end_time"
                    type="time"
                    value="{{ old('end_time') }}"
                />
            </div>

            {{-- Motivo --}}
            <div class="lg:col-span-2">
                <x-wire-textarea
                    label="Motivo de la consulta *"
                    name="reason"
                    placeholder="Describe el motivo de la consulta..."
                >{{ old('reason') }}</x-wire-textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <x-wire-button outline gray href="{{ route('admin.appointments.index') }}">
                Cancelar
            </x-wire-button>
            <x-wire-button type="submit" primary>
                <i class="fa-solid fa-check me-1"></i> Confirmar cita
            </x-wire-button>
        </div>
    </x-wire-card>
</form>

</x-admin-layout>
