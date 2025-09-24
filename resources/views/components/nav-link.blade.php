@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-sacli-green-500 text-sm font-medium leading-5 text-sacli-green-700 focus:outline-none focus:border-sacli-green-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-sacli-green-700 hover:border-sacli-green-300 focus:outline-none focus:text-sacli-green-700 focus:border-sacli-green-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
