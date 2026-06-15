@php
    $colors = [
        1 => 'bg-green-100 text-green-800',
        2 => 'bg-blue-100 text-blue-800',
        3 => 'bg-red-100 text-red-800',
    ];
    $labels = [
        1 => 'Programado',
        2 => 'Completado',
        3 => 'Cancelado',
    ];
    $color = $colors[$appointment->status] ?? 'bg-gray-100 text-gray-800';
    $label = $labels[$appointment->status] ?? 'Desconocido';
@endphp
<span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">
    {{ $label }}
</span>
