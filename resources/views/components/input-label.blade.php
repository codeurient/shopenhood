@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[12px] font-medium text-[#37474F]']) }}>
    {{ $value ?? $slot }}
</label>
