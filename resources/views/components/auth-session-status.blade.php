@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg bg-[#0F8A72]/10 border border-[#0F8A72]/20 p-3 text-sm text-[#0F8A72]']) }}>
        {{ $status }}
    </div>
@endif
