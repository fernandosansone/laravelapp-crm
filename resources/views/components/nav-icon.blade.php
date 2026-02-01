@props(['name' => ''])

@php
  $iconClass = 'w-5 h-5 shrink-0';
@endphp

@if($name === 'dashboard')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4.5 10.5V21h5.25v-6h4.5v6H19.5V10.5" />
  </svg>

@elseif($name === 'calendar')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 9h16M6 5h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 13h4m-4 4h8" />
  </svg>

@elseif($name === 'users')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.004 9.004 0 01-6 0M18 20a2 2 0 002-2v-1a4 4 0 00-4-4h-1m-6 0H8a4 4 0 00-4 4v1a2 2 0 002 2m10-11a4 4 0 11-8 0 4 4 0 018 0z" />
  </svg>

@elseif($name === 'doc')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
  </svg>

@elseif($name === 'chart')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5a2 2 0 012-2h12a2 2 0 012 2v14" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 17V9m4 8V7m4 10v-5" />
  </svg>

@elseif($name === 'user')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 7a4 4 0 110 8 4 4 0 010-8z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M20 8v6m3-3h-6" />
  </svg>

@elseif($name === 'layers')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4-8 4-8-4 8-4z" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 11l8 4 8-4" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 15l8 4 8-4" />
  </svg>

@elseif($name === 'clock')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12a8 8 0 11-16 0 8 8 0 0116 0z" />
  </svg>

@elseif($name === 'logout')
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
    <path stroke-linecap="round" stroke-linejoin="round"
          d="M18 12H9m0 0l3-3m-3 3l3 3" />
  </svg>

@else
  <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16" />
  </svg>
@endif
