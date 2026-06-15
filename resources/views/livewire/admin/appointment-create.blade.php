<div>
    <x-wire-card>

        {{-- ── Buscar disponibilidad ───────────────────────────────────────── --}}
        <div class="mb-6">
            <h2 class="text-base font-semibold text-gray-900 mb-0.5">Buscar disponibilidad</h2>
            <p class="text-sm text-gray-500 mb-4">Encuentra el horario perfecto para tu cita.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">

                <div>
                    <x-wire-input
                        label="Fecha"
                        wire:model.live="searchDate"
                        type="date"
                        min="{{ now()->format('Y-m-d') }}"
                    />
                    @error('searchDate')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-wire-native-select label="Hora" wire:model.live="searchHour">
                        <option value="">Selecciona una hora</option>
                        @for ($h = 8; $h <= 19; $h++)
                            <option value="{{ $h }}">
                                {{ sprintf('%02d', $h) }}:00:00 – {{ sprintf('%02d', $h + 1) }}:00:00
                            </option>
                        @endfor
                    </x-wire-native-select>
                    @error('searchHour')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <x-wire-native-select label="Especialidad (opcional)" wire:model.live="searchSpecialtyId">
                        <option value="">Todas las especialidades</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                <div>
                    <x-wire-button
                        wire:click="searchAvailability"
                        wire:loading.attr="disabled"
                        wire:target="searchAvailability"
                        primary
                        class="w-full justify-center"
                    >
                        <span wire:loading.remove wire:target="searchAvailability">Buscar disponibilidad</span>
                        <span wire:loading wire:target="searchAvailability">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Buscando…
                        </span>
                    </x-wire-button>
                </div>

            </div>
        </div>

        <hr class="border-gray-100 mb-6">

        {{-- ── Resultados + Resumen (siempre visible) ─────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ── Columna izquierda: doctores disponibles ──────────────── --}}
            <div>
                @if (!$searched)
                    <div class="flex flex-col items-center justify-center py-16 text-gray-300
                                border border-dashed border-gray-200 rounded-xl">
                        <i class="fa-solid fa-magnifying-glass text-4xl mb-3"></i>
                        <p class="text-sm">Usa el buscador para ver los doctores disponibles.</p>
                    </div>

                @elseif (count($doctorsWithSlots) === 0)
                    <div class="flex flex-col items-center justify-center py-16 text-gray-400
                                border border-gray-200 rounded-xl">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
                        <p class="text-sm font-medium">No hay horarios disponibles</p>
                        <p class="text-xs mt-1 text-gray-300">Intenta con otra fecha, hora o especialidad.</p>
                    </div>

                @else
                    <div class="space-y-3 max-h-[520px] overflow-y-auto pr-1">
                        @foreach ($doctorsWithSlots as $doctor)
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-indigo-200 transition-colors
                                    {{ $selectedDoctorId === $doctor['id'] ? 'border-indigo-300 bg-indigo-50/40' : '' }}">

                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center
                                            text-white text-sm font-bold flex-shrink-0 select-none">
                                    {{ $doctor['initials'] }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $doctor['name'] }}</p>
                                    <p class="text-xs text-indigo-600">{{ $doctor['specialty'] }}</p>
                                </div>
                            </div>

                            <p class="text-xs text-gray-400 mb-2 font-medium uppercase tracking-wide">
                                Horarios disponibles:
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($doctor['slots'] as $slot)
                                    @php $isSelected = $selectedDoctorId === $doctor['id'] && $selectedStartTime === $slot; @endphp
                                    <button
                                        wire:click="selectSlot({{ $doctor['id'] }}, '{{ addslashes($doctor['name']) }}', '{{ $slot }}')"
                                        class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors
                                               {{ $isSelected
                                                    ? 'bg-indigo-600 text-white shadow-sm'
                                                    : 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100' }}"
                                    >
                                        {{ $slot }}
                                    </button>
                                @endforeach
                            </div>

                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Columna derecha: Resumen de la cita (SIEMPRE VISIBLE) ── --}}
            <div>
                <div class="border border-gray-200 rounded-xl p-5 h-full">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Resumen de la cita</h3>

                    {{-- Datos del slot (grises si no hay selección) --}}
                    <div class="space-y-2 text-sm mb-5 bg-gray-50 rounded-lg p-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Doctor:</span>
                            <span class="font-medium {{ $selectedDoctorId ? 'text-gray-900' : 'text-gray-300' }}">
                                {{ $selectedDoctorName ?: '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fecha:</span>
                            <span class="font-medium {{ $selectedDate ? 'text-gray-900' : 'text-gray-300' }}">
                                {{ $selectedDate ? \Carbon\Carbon::parse($selectedDate)->format('Y-m-d') : '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Horario:</span>
                            <span class="font-medium {{ $selectedStartTime ? 'text-gray-900' : 'text-gray-300' }}">
                                {{ $selectedStartTime ? $selectedStartTime . ':00 – ' . $selectedEndTime . ':00' : '—' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Duración:</span>
                            <span class="font-medium text-gray-900">15 minutos</span>
                        </div>
                    </div>

                    {{-- Paciente --}}
                    <div class="mb-4">
                        <x-wire-native-select label="Paciente" wire:model="patientId">
                            <option value="">Selecciona un paciente</option>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->user->name }}</option>
                            @endforeach
                        </x-wire-native-select>
                        @error('patientId')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Motivo --}}
                    <div class="mb-5">
                        <x-wire-textarea
                            label="Motivo de la cita"
                            wire:model="reason"
                            placeholder="Describe el motivo de la consulta..."
                            rows="3"
                        />
                        @error('reason')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Error si intentan guardar sin slot --}}
                    @error('selectedDoctorId')
                        <p class="text-xs text-red-600 mb-3">
                            <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                        </p>
                    @enderror

                    <x-wire-button
                        wire:click="store"
                        wire:loading.attr="disabled"
                        wire:target="store"
                        primary
                        class="w-full justify-center"
                    >
                        <span wire:loading.remove wire:target="store">
                            <i class="fa-solid fa-check me-1"></i> Confirmar cita
                        </span>
                        <span wire:loading wire:target="store">
                            <i class="fa-solid fa-spinner fa-spin me-1"></i> Guardando…
                        </span>
                    </x-wire-button>
                </div>
            </div>

        </div>
    </x-wire-card>
</div>
