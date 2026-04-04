@if(isset($tenant) && $tenant->logo_original)
    {{-- Logo customizada do Cliente (Vinda do Storage) --}}
    <img src="{{ asset('storage/' . $tenant->logo_original) }}" {{ $attributes->merge(['class' => 'h-9 w-auto']) }}>
@else
    {{-- Logo padrão da CodeForce --}}
    {{-- O asset() já aponta automaticamente para a pasta /public --}}
    <img src="{{ asset('logo-codeforce-02.png') }}" {{ $attributes->merge(['class' => 'h-9 w-auto']) }}>
@endif