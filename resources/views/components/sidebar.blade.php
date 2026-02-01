@php
  $navItemClass = function(string $pathPrefix, ?string $routeName = null) {
    $active = $routeName
      ? request()->routeIs($routeName)
      : request()->is($pathPrefix . '*');

    $base = 'flex items-center justify-between gap-3 px-3 py-2 rounded-xl text-sm font-medium transition';
    return $active
      ? $base . ' bg-gray-900 text-white'
      : $base . ' text-gray-700 hover:bg-gray-100';
  };

  $iconClass = 'w-5 h-5 shrink-0';

  $badgeClass = function(?string $variant) {
    return match($variant) {
      'red' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
      'green' => 'bg-green-50 text-green-700 ring-1 ring-green-200',
      default => 'bg-gray-50 text-gray-700 ring-1 ring-gray-200',
    };
  };

  $renderIcon = function(string $icon) use ($iconClass) {
    // Devuelve SVG inline según "icon"
    return match($icon) {
      'dashboard' => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4.5 10.5V21h5.25v-6h4.5v6H19.5V10.5" />
        </svg>
      SVG,
      'calendar' => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 9h16M6 5h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 13h4m-4 4h8" />
        </svg>
      SVG,
      'users' => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.004 9.004 0 01-6 0M18 20a2 2 0 002-2v-1a4 4 0 00-4-4h-1m-6 0H8a4 4 0 00-4 4v1a2 2 0 002 2m10-11a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
      SVG,
      'doc' => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
        </svg>
      SVG,
      'chart' => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5a2 2 0 012-2h12a2 2 0 012 2v14" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 17V9m4 8V7m4 10v-5" />
        </svg>
      SVG,
      'user' => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 7a4 4 0 110 8 4 4 0 010-8z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 8v6m3-3h-6" />
        </svg>
      SVG,
      'layers' => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l8 4-8 4-8-4 8-4z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 11l8 4 8-4" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 15l8 4 8-4" />
        </svg>
      SVG,
      'clock' => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M20 12a8 8 0 11-16 0 8 8 0 0116 0z" />
        </svg>
      SVG,
      default => <<<SVG
        <svg class="$iconClass" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h16" />
        </svg>
      SVG,
    };
  };
@endphp

<aside class="h-full w-72 bg-white ring-1 ring-gray-100 flex flex-col">
  {{-- Brand --}}
  <div class="p-5 border-b border-gray-100">
    <div class="flex items-center gap-3">
      <img src="{{ asset('brand/logo.png') }}"
          alt="RCg"
          class="h-10 w-10 rounded-full ring-1 ring-gray-200 bg-white p-1 object-contain">
      <div class="min-w-0">
        <div class="text-sm text-gray-500 leading-none">RCg</div>
        <div class="text-base font-semibold text-gray-900 leading-tight">
          Gestión Comercial <span class="text-gray-500">(CRM)</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Menu --}}
  <nav class="p-4 space-y-1 flex-1 overflow-y-auto pb-28">
    @foreach(($menu ?? []) as $item)
      @php
        $prefix = $item['activePathPrefix'] ?? ($item['key'] ?? '');
        $routeName = $item['route'] ?? null;

        $badge = $item['badge'] ?? null;
        $badgeVariant = $item['badgeVariant'] ?? null;

        $activeClass = $navItemClass($prefix, $routeName);
      @endphp

      <a href="{{ route($routeName) }}" class="{{ $activeClass }}">
        <div class="flex items-center gap-3">
          {!! $renderIcon($item['icon'] ?? '') !!}
          <span>{{ $item['label'] ?? '' }}</span>
        </div>

        @if(!is_null($badge))
          @php $bd = is_numeric($badge) ? (int)$badge : $badge; @endphp
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass($badgeVariant) }}">
            {{ $bd }}
          </span>
        @endif
      </a>
    @endforeach
  </nav>

  {{-- Sticky: Usuario + Logout siempre visible --}}
  <div class="sticky bottom-0 bg-white border-t border-gray-100 p-4">
    <div class="flex items-center justify-between gap-3">
      <div class="min-w-0">
        <div class="text-xs text-gray-500">Conectado como</div>
        <div class="flex items-center gap-2 mt-0.5 min-w-0">
          <div class="text-sm font-medium text-gray-900 truncate">
            {{ $userName ?? (auth()->user()->name ?? '') }}
          </div>

          @if(!empty($primaryRole))
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-900 text-white shrink-0">
              {{ $primaryRole }}
            </span>
          @endif
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
      @csrf
      <button type="submit"
        class="w-full flex items-center justify-between gap-3 px-3 py-2 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition">
        <div class="flex items-center gap-3">
          <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H9m0 0l3-3m-3 3l3 3" />
          </svg>
          <span>Cerrar sesión</span>
        </div>

        <span class="text-xs text-red-500">Salir</span>
      </button>
    </form>
  </div>
</aside>
