@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-secondary']) }}>
    {{ $value ?? $slot }}
</label>
