@props(['tab', 'error' => false])

<li class="me-2">
    <a href="# x-on:click=tab = '{{ $tab }}'"
        :class="{
            'text-red-600 border-red-600': {{ $hasError ? 'true' : 'false' }} && tab !== '{{ $tab }}',
            'text-blue-600 border-blue-600 active': tab === '{{ $tab }}' && {{ $hasError ? 'true' : 'false' }},
            'text-red-600 border-red-600 active': tab === '{{ $tab }}' && {{ $hasError ? 'true' : 'false' }},
            'border-transparent hover:text-blue-600 hover:border-gray-300': tab !== '{{ $tab }}' && {{ $hasError ? 'true' : 'false' }}
        }"
        class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group transition-colors duration-200
        {{ $hasError ? 'text-red-600 border-red-600' : '' }}"
        :aria-current="tab === 'antecedentes' ? 'page' : undefined">
        {{ $slot }}
        @if ($error)
        <i class="fas fa-exclamation ms-2 animate-pulse"></i>
        @endif
    ></a>
</li>
