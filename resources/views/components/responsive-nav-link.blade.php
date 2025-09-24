@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-sacli-green-500 text-start text-base font-medium text-sacli-green-700 bg-sacli-green-50 focus:outline-none focus:text-sacli-green-800 focus:bg-sacli-green-100 focus:border-sacli-green-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-sacli-green-800 hover:bg-sacli-green-50 hover:border-sacli-green-300 focus:outline-none focus:text-sacli-green-800 focus:bg-sacli-green-50 focus:border-sacli-green-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
