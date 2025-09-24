@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-sacli-green-700']) }}>
    {{ $value ?? $slot }}
</label>
