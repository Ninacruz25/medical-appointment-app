<x-admin-layout title="Editar cita" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Citas', 'href' => route('admin.appointments.index')],
    ['name' => 'Editar'],
]">

<form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
    @csrf
    @method('PATCH')

    <x-wire-card>
        <div class="grid lg:grid-cols-2 gap-6">

            {{-- Paciente --}}
            <div>
                <x-wire-native-select label="Paciente *" name="patient_id">
                    <option value="">Selecciona un paciente</option>
                    @foreach ($patients as $patient)
                        <option value="{{ $patient->id }}"
                            @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                            {{ $patient->user->name }}
                        </option>
                    @endforeach
                </x-wire-native-select>
                @error('patient_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Doctor --}}
            <div>
                <x-wire-native-select label="Doctor *" name="doctor_id">
                    <option value="">Selecciona un doctor</option>
                    @foreach ($doctors as $doctor)
                        <option value="{{ $doctor->id }}"
                            @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>
                            {{ $doctor->user->name }}
                            @if($doctor->specialty)
                                — {{ $doctor->specialty->name }}
                            @endif
                        </option>
                    @endforeach
                </x-wire-native-select>
                @error('doctor_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha --}}
            <div>
                <x-wire-input
                    label="Fecha *"
                    name="date"
                    type="date"
                    value="{{ old('date', $appointment->date->format('Y-m-d')) }}"
                />
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div>
                <x-wire-native-select label="Estado *" name="status">
                    <option value="1" @selected(old('status', $appointment->status) == 1)>Programado</option>
                    <option value="2" @selected(old('status', $appointment->status) == 2)>Completado</option>
                    <option value="3" @selected(old('status', $appointment->status) == 3)>Cancelado</option>
                </x-wire-native-select>
                @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hora inicio --}}
            <div>
                <x-wire-input
                    label="Hora de inicio *"
                    name="start_time"
                    type="time"
                    value="{{ old('start_time', substr($appointment->start_time, 0, 5)) }}"
                />
                @error('start_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hora fin --}}
            <div>
                <x-wire-input
                    label="Hora de fin *"
                    name="end_time"
                    type="time"
                    value="{{ old('end_time', substr($appointment->end_time, 0, 5)) }}"
                />
                @error('end_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Motivo --}}
            <div class="lg:col-span-2">
                <x-wire-textarea
                    label="Motivo de la consulta *"
                    name="reason"
                    placeholder="Describe el motivo de la consulta..."
                >{{ old('reason', $appointment->reason) }}</x-wire-textarea>
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex justify-end gap-3 mt-6">
            <x-wire-button outline gray href="{{ route('admin.appointments.index') }}">
                Cancelar
            </x-wire-button>
            <x-wire-button type="submit" primary>
                <i class="fa-solid fa-floppy-disk me-1"></i> Guardar cambios
            </x-wire-button>
        </div>
    </x-wire-card>
</form>

</x-admin-layout>
