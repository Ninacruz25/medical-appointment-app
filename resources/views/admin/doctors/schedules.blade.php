<x-admin-layout title="Horarios" :breadcrumbs="[
    ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
    ['name' => 'Doctores',  'href' => route('admin.doctors.index')],
    ['name' => 'Horarios'],
]">

@php
    $hours = range(8, 19);
    $days = [
        'lun' => 'Lun',
        'mar' => 'Mar',
        'mie' => 'Mié',
        'jue' => 'Jue',
        'vie' => 'Vie',
        'sab' => 'Sáb',
        'dom' => 'Dom',
    ];
@endphp

{{-- Formulario oculto que recibe el JSON de Alpine y hace el POST --}}
<form id="scheduleForm" action="{{ route('admin.doctors.schedules.save', $doctor) }}" method="POST">
    @csrf
    <input type="hidden" name="slots_json" id="slotsJson">
</form>

<div x-data="scheduleManager()" x-cloak>

    {{-- Header --}}
    <x-wire-card class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ $doctor->user->profile_photo_url }}"
                     alt="{{ $doctor->user->name }}"
                     class="h-14 w-14 rounded-full object-cover">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Gestor de Horarios</h2>
                    <p class="text-sm text-gray-500">Dr. {{ $doctor->user->name }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">
                    <i class="fa-solid fa-arrow-left me-1"></i> Volver
                </x-wire-button>
                <button
                    type="button"
                    x-on:click="save()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <i class="fa-solid fa-floppy-disk"></i> Guardar horario
                </button>
            </div>
        </div>
    </x-wire-card>

    {{-- Grilla de horarios --}}
    <x-wire-card>
        <div class="overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-3 py-3 text-left text-gray-600 font-semibold border border-gray-200 w-24">DÍA/HORA</th>
                        <th class="px-3 py-3 text-left text-gray-500 font-medium border border-gray-200 w-36">Rango</th>
                        @foreach($days as $label)
                        <th class="px-3 py-3 text-center text-gray-600 font-semibold border border-gray-200 min-w-[80px]">{{ $label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($hours as $hour)
                    @php
                        $hourKey    = sprintf('%02d', $hour);
                        $slotRanges = [
                            ['key' => $hourKey . '00', 'label' => sprintf('%02d:00 – %02d:15', $hour, $hour)],
                            ['key' => $hourKey . '15', 'label' => sprintf('%02d:15 – %02d:30', $hour, $hour)],
                            ['key' => $hourKey . '30', 'label' => sprintf('%02d:30 – %02d:45', $hour, $hour)],
                            ['key' => $hourKey . '45', 'label' => sprintf('%02d:45 – %02d:00', $hour, $hour + 1)],
                        ];
                    @endphp

                    {{-- Fila "Todos" del grupo de hora --}}
                    <tr class="border-t-2 border-gray-300 bg-gray-50">
                        <td class="px-3 py-2 font-bold text-gray-700 border border-gray-200 text-center align-middle"
                            rowspan="5">
                            {{ sprintf('%02d:00', $hour) }}<br>
                            <span class="text-gray-400 font-normal">–</span><br>
                            {{ sprintf('%02d:00', $hour + 1) }}
                        </td>
                        <td class="px-3 py-2 font-semibold text-gray-700 border border-gray-200">
                            Todos
                        </td>
                        @foreach(array_keys($days) as $day)
                        <td class="px-3 py-2 border border-gray-200 text-center">
                            <input
                                type="checkbox"
                                class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer"
                                x-on:change="toggleHourDay('{{ $hourKey }}', '{{ $day }}', $event.target.checked)"
                                :checked="allHourDayChecked('{{ $hourKey }}', '{{ $day }}')"
                            >
                        </td>
                        @endforeach
                    </tr>

                    {{-- 4 filas de slots (15 min) --}}
                    @foreach($slotRanges as $slot)
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="px-3 py-1.5 text-gray-500 border border-gray-200 bg-white">
                            {{ $slot['label'] }}
                        </td>
                        @foreach(array_keys($days) as $day)
                        <td class="px-3 py-1.5 border border-gray-200 text-center">
                            <input
                                type="checkbox"
                                class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer"
                                x-model="slots['{{ $slot['key'] }}_{{ $day }}']"
                            >
                        </td>
                        @endforeach
                    </tr>
                    @endforeach

                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Leyenda --}}
        <div class="mt-4 flex items-center gap-4 text-xs text-gray-500">
            <div class="flex items-center gap-1">
                <input type="checkbox" checked class="w-3 h-3 text-blue-600 rounded" disabled>
                <span>Disponible</span>
            </div>
            <div class="flex items-center gap-1">
                <input type="checkbox" class="w-3 h-3 rounded" disabled>
                <span>No disponible</span>
            </div>
            <span>·</span>
            <span>Horario: 08:00 – 20:00 · Intervalos de 15 min</span>
        </div>
    </x-wire-card>
</div>

<script>
    function scheduleManager() {
        return {
            // Object.assign fuerza que sea siempre un objeto JS (nunca array)
            // @json([]) produce "[]" en JS; JSON.stringify([]) ignora propiedades string → bug
            slots: Object.assign({}, @json($existingSlots)),
            days: ['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'],
            minutes: ['00', '15', '30', '45'],

            toggleHourDay(hour, day, checked) {
                this.minutes.forEach(m => {
                    this.slots[`${hour}${m}_${day}`] = checked;
                });
            },

            allHourDayChecked(hour, day) {
                return this.minutes.every(m => !!this.slots[`${hour}${m}_${day}`]);
            },

            save() {
                document.getElementById('slotsJson').value = JSON.stringify(this.slots);
                document.getElementById('scheduleForm').submit();
            },
        }
    }
</script>

</x-admin-layout>
