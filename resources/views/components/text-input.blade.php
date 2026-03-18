@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'w-full px-3 h-[34px] border border-[#E0E0E0] text-[#1A1A1A] text-[13px] focus:border-[#D4AF37] focus:ring-0 focus:outline-none rounded bg-white transition disabled:bg-gray-50 disabled:text-[#37474F]']) !!}>
