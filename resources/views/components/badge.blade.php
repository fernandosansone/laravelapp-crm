@props(['variant' => 'default'])

@php
$classes = match($variant) {
  'success' => 'bg-green-50 text-green-700 ring-green-200',
  'warning' => 'bg-amber-50 text-amber-700 ring-amber-200',
  'danger'  => 'bg-red-50 text-red-700 ring-red-200',
  'info'    => 'bg-blue-50 text-blue-700 ring-blue-200',
  default   => 'bg-gray-50 text-gray-700 ring-gray-200',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ring-1 {$classes}"]) }}>
  {{ $slot }}
</span>
