@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-[#333333]']) }}>
    {{ $value ?? $slot }}
</label>
