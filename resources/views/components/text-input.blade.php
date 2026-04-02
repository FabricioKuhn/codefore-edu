@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-codeforce-green focus:ring-codeforce-green rounded-md shadow-sm']) }}>
