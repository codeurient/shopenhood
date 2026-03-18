<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center h-[34px] px-6 bg-[#D4AF37] border border-transparent rounded font-semibold text-[13px] text-[#000000] tracking-wide hover:brightness-110 focus:outline-none transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
