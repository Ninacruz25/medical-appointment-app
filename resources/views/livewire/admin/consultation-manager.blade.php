@php
    $tabErrors = [
        'consulta' => ['diagnosis', 'treatment', 'notes'],
        'receta'   => array_map(fn($i) => "medicines.$i.medication", array_keys($medicines))
                    + array_map(fn($i) => "medicines.$i.dose", array_keys($medicines))
                    + array_map(fn($i) => "medicines.$i.frequency", array_keys($medicines)),
    ];
    $activeTab = 'consulta';
    if ($errors->hasAny(array_merge(
        array_map(fn($i) => "medicines.$i.medication", array_keys($medicines)),
        array_map(fn($i) => "medicines.$i.dose", array_keys($medicines)),
        array_map(fn($i) => "medicines.$i.frequency", array_keys($medicines))
    ))) {
        $activeTab = 'receta';
    }
    $hasConsultaError = $errors->hasAny(['diagnosis', 'treatment', 'notes']);
    $hasRecetaError   = $activeTab === 'receta';
@endphp

<div>
    {{-- Header del paciente --}}
    <x-wire-card class="mb-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $appointment->patient->user->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    DNI: {{ $appointment->patient->user->id_number ?? '—' }}
                </p>
            </div>

            <div class="flex gap-2 flex-wrap shrink-0">
                <x-wire-button outline gray href="{{ route('admin.appointments.index') }}">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver
                </x-wire-button>

                <x-wire-button outline blue wire:click="$set('showMedicalHistory', true)">
                    <i class="fa-solid fa-file-medical me-1"></i> Ver Historia
                </x-wire-button>

                <x-wire-button outline indigo wire:click="$set('showPreviousConsultations', true)">
                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Consultas Anteriores
                </x-wire-button>
            </div>
        </div>
    </x-wire-card>

    {{-- Card con tabs --}}
    <x-wire-card>
        <div x-data="{ tab: '{{ $activeTab }}' }">

            {{-- Menú de pestañas --}}
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-6">

                {{-- Tab Consulta --}}
                <li class="me-2">
                    <a href="#" x-on:click.prevent="tab = 'consulta'"
                       :class="{
                           'text-blue-600 border-blue-600 active': tab === 'consulta' && !{{ $hasConsultaError ? 'true' : 'false' }},
                           'text-red-600 border-red-600 active':   tab === 'consulta' && {{ $hasConsultaError ? 'true' : 'false' }},
                           'text-red-600 border-red-600':          tab !== 'consulta' && {{ $hasConsultaError ? 'true' : 'false' }},
                           'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'consulta' && !{{ $hasConsultaError ? 'true' : 'false' }}
                       }"
                       class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200 {{ $hasConsultaError ? 'text-red-600 border-red-600' : '' }}">
                        <i class="fa-solid fa-stethoscope me-2"></i>
                        Consulta
                        @if($hasConsultaError)
                            <i class="fa-solid fa-circle-exclamation ms-2 animate-pulse"></i>
                        @endif
                    </a>
                </li>

                {{-- Tab Receta --}}
                <li class="me-2">
                    <a href="#" x-on:click.prevent="tab = 'receta'"
                       :class="{
                           'text-blue-600 border-blue-600 active': tab === 'receta' && !{{ $hasRecetaError ? 'true' : 'false' }},
                           'text-red-600 border-red-600 active':   tab === 'receta' && {{ $hasRecetaError ? 'true' : 'false' }},
                           'text-red-600 border-red-600':          tab !== 'receta' && {{ $hasRecetaError ? 'true' : 'false' }},
                           'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'receta' && !{{ $hasRecetaError ? 'true' : 'false' }}
                       }"
                       class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200 {{ $hasRecetaError ? 'text-red-600 border-red-600' : '' }}">
                        <i class="fa-solid fa-prescription-bottle-medical me-2"></i>
                        Receta
                        @if($hasRecetaError)
                            <i class="fa-solid fa-circle-exclamation ms-2 animate-pulse"></i>
                        @endif
                    </a>
                </li>
            </ul>

            {{-- Contenido Tab: Consulta --}}
            <div x-show="tab === 'consulta'">
                <div class="space-y-4">
                    <div>
                        <x-wire-textarea
                            label="Diagnóstico *"
                            wire:model="diagnosis"
                            placeholder="Describa el diagnóstico del paciente..."
                            rows="4"
                        />
                        @error('diagnosis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-wire-textarea
                            label="Tratamiento *"
                            wire:model="treatment"
                            placeholder="Describa el tratamiento indicado..."
                            rows="4"
                        />
                        @error('treatment')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <x-wire-textarea
                            label="Notas adicionales"
                            wire:model="notes"
                            placeholder="Observaciones o notas adicionales (opcional)..."
                            rows="3"
                        />
                    </div>
                </div>
            </div>

            {{-- Contenido Tab: Receta --}}
            <div x-show="tab === 'receta'" style="display: none;">
                <div class="mb-4">
                    <x-wire-button wire:click="addMedicine" outline blue sm>
                        <i class="fa-solid fa-plus me-1"></i> Añadir Medicamento
                    </x-wire-button>
                </div>

                @if(count($medicines) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3">Medicamento *</th>
                                <th class="px-4 py-3">Dosis *</th>
                                <th class="px-4 py-3">Frecuencia / Duración *</th>
                                <th class="px-4 py-3 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($medicines as $index => $medicine)
                            <tr wire:key="medicine-{{ $index }}">
                                <td class="px-4 py-2">
                                    <x-wire-input
                                        wire:model="medicines.{{ $index }}.medication"
                                        placeholder="Ej. Paracetamol"
                                    />
                                    @error("medicines.$index.medication")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-4 py-2">
                                    <x-wire-input
                                        wire:model="medicines.{{ $index }}.dose"
                                        placeholder="Ej. 500mg"
                                    />
                                    @error("medicines.$index.dose")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-4 py-2">
                                    <x-wire-input
                                        wire:model="medicines.{{ $index }}.frequency"
                                        placeholder="Ej. Cada 8 horas por 5 días"
                                    />
                                    @error("medicines.$index.frequency")
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <x-wire-button wire:click="removeMedicine({{ $index }})" red xs>
                                        <i class="fa-solid fa-trash"></i>
                                    </x-wire-button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-12 text-gray-400">
                    <i class="fa-solid fa-prescription-bottle text-4xl mb-3"></i>
                    <p class="text-sm">No hay medicamentos en la receta.</p>
                    <p class="text-xs">Haz clic en "Añadir Medicamento" para agregar uno.</p>
                </div>
                @endif
            </div>

            {{-- Botón guardar --}}
            <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
                <x-wire-button wire:click="saveConsultation" wire:loading.attr="disabled" primary>
                    <span wire:loading.remove>
                        <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Consulta
                    </span>
                    <span wire:loading>
                        <i class="fa-solid fa-spinner fa-spin me-1"></i> Guardando...
                    </span>
                </x-wire-button>
            </div>
        </div>
    </x-wire-card>

    {{-- Modal: Consultas Anteriores --}}
    <x-modal wire:model="showPreviousConsultations" maxWidth="2xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fa-solid fa-clock-rotate-left me-2 text-indigo-500"></i>
                    Consultas Anteriores
                </h3>
                <button wire:click="$set('showPreviousConsultations', false)"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            @if($previousConsultations->isEmpty())
                <div class="text-center py-10 text-gray-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
                    <p class="text-sm">No hay consultas anteriores registradas.</p>
                </div>
            @else
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    @foreach($previousConsultations as $consultation)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-sm font-medium text-gray-900">
                                <i class="fa-solid fa-calendar-day me-1 text-indigo-400"></i>
                                {{ $consultation->appointment->date->format('d/m/Y') }}
                                a las {{ $consultation->appointment->start_time }}
                            </div>
                            <span class="text-xs text-gray-500">
                                Dr. {{ $consultation->appointment->doctor->user->name ?? '—' }}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 gap-2 text-sm">
                            <div>
                                <span class="font-semibold text-gray-600">Diagnóstico:</span>
                                <p class="text-gray-800 mt-0.5">{{ $consultation->diagnosis }}</p>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-600">Tratamiento:</span>
                                <p class="text-gray-800 mt-0.5">{{ $consultation->treatment }}</p>
                            </div>
                            @if($consultation->notes)
                            <div>
                                <span class="font-semibold text-gray-600">Notas:</span>
                                <p class="text-gray-800 mt-0.5">{{ $consultation->notes }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="flex justify-end mt-3 pt-2 border-t border-gray-100">
                            <x-wire-button outline blue xs>
                                <i class="fa-solid fa-eye me-1"></i> Consultar Detalle
                            </x-wire-button>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            <div class="flex justify-end mt-4">
                <x-wire-button outline gray wire:click="$set('showPreviousConsultations', false)">
                    Cerrar
                </x-wire-button>
            </div>
        </div>
    </x-modal>

    {{-- Modal: Historia Médica --}}
    <x-modal wire:model="showMedicalHistory" maxWidth="xl">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fa-solid fa-file-medical me-2 text-blue-500"></i>
                    Historia Médica
                </h3>
                <button wire:click="$set('showMedicalHistory', false)"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            @php $patient = $appointment->patient; @endphp

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Tipo de sangre</span>
                    <p class="text-gray-900 font-medium">{{ $patient->bloodType?->name ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Alergias</span>
                    <p class="text-gray-800">{{ $patient->allergies ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Enf. crónicas</span>
                    <p class="text-gray-800">{{ $patient->chronic_conditions ?? '—' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide block mb-1">Ant. quirúrgicos</span>
                    <p class="text-gray-800">{{ $patient->surgical_history ?? '—' }}</p>
                </div>
            </div>

            <div class="flex justify-between items-center mt-6">
                <x-wire-button outline gray wire:click="$set('showMedicalHistory', false)">
                    Cerrar
                </x-wire-button>
                <x-wire-button primary href="{{ route('admin.patients.edit', $appointment->patient) }}">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Ver / Editar Historia Médica
                </x-wire-button>
            </div>
        </div>
    </x-modal>
</div>
