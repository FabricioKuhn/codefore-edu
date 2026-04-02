<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-codeforce-green border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#008f7f] focus:bg-[#008f7f] active:bg-[#007a6c] focus:outline-none focus:ring-2 focus:ring-codeforce-green focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
