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

  $badgeClass = function(?string $variant) {
    return match($variant) {
      'red' => 'bg-red-50 text-red-700 ring-1 ring-red-200',
      'green' => 'bg-green-50 text-green-700 ring-1 ring-green-200',
      default => 'bg-gray-50 text-gray-700 ring-1 ring-gray-200',
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

  {{-- Menu (secciones) --}}
  <nav class="p-4 space-y-5 flex-1 overflow-y-auto pb-28">
    @foreach(($menu ?? []) as $section)
      <div class="space-y-1">
        <div class="px-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
          {{ $section['title'] ?? '' }}
        </div>

        <div class="space-y-1 mt-2">
          @foreach(($section['items'] ?? []) as $item)
            @php
              $prefix = $item['activePathPrefix'] ?? ($item['key'] ?? '');
              $routeName = $item['route'] ?? null;

              $badge = $item['badge'] ?? null;
              $badgeVariant = $item['badgeVariant'] ?? null;

              $activeClass = $navItemClass($prefix, $routeName);
            @endphp

            <a href="{{ route($routeName) }}" class="{{ $activeClass }}">
              <div class="flex items-center gap-3 min-w-0">
                <x-nav-icon :name="$item['icon'] ?? ''" />
                <span class="truncate">{{ $item['label'] ?? '' }}</span>
              </div>

              @if(!is_null($badge))
                @php $bd = is_numeric($badge) ? (int)$badge : $badge; @endphp
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass($badgeVariant) }}">
                  {{ $bd }}
                </span>
              @endif
            </a>
          @endforeach
        </div>
      </div>
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
          <x-nav-icon name="logout" />
          <span>Cerrar sesión</span>
        </div>
        <span class="text-xs text-red-500">Salir</span>
      </button>
    </form>
  </div>
</aside>
