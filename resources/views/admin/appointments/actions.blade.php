<div class="flex gap-1">
    {{-- Editar --}}
    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    {{-- Atender consulta --}}
    <x-wire-button href="{{ route('admin.appointments.consultation', $appointment) }}" green xs>
        <i class="fa-solid fa-stethoscope"></i>
    </x-wire-button>

    {{-- Eliminar --}}
    <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST"
          onsubmit="return confirm('¿Eliminar esta cita?')">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
