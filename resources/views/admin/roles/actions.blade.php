<div class="flex items-center space-x-2">
    @if ($role->is_system)
        {{-- Roles Protegidos --}}
        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-md text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200 select-none">
            <i class="fa-solid fa-lock text-[10px]"></i>
            Sistema
        </span>
        @else
        {{-- Roles Normales --}}


        <x-wire-button href="{{ route('admin.roles.edit', $role) }}" blue xs>
            <i class="fa-solid fa-pen-to-square"></i>
        </x-wire-button>
    
    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="delete-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
    @endif
</div>