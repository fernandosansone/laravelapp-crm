@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl shadow-sm ring-1 ring-gray-100']) }}>
    @if($title || $subtitle)
        <div class="px-6 pt-5 pb-4 border-b border-gray-100">
            @if($title)
                <div class="text-lg font-semibold text-gray-900">{{ $title }}</div>
            @endif
            @if($subtitle)
                <div class="text-sm text-gray-500 mt-1">{{ $subtitle }}</div>
            @endif
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>
</div>
