@php
  use Illuminate\Support\Facades\DB;

  $today = \Illuminate\Support\Carbon::today()->toDateString();
  $userId = auth()->id();

  // ---- Conteo de atrasos (oportunidades asignadas al usuario cuyo último followup tiene next_contact_date < hoy)

  // Subquery: último followup por oportunidad (MAX(contact_date))
  $latestFollowup = DB::table('opportunity_followups as f')
    ->select('f.opportunity_id', DB::raw('MAX(f.contact_date) as last_contact_date'))
    ->groupBy('f.opportunity_id');

  // Subquery: next_contact_date del followup más reciente
  $lastFollowupData = DB::table('opportunity_followups as f')
    ->joinSub($latestFollowup, 'lf', function ($join) {
      $join->on('lf.opportunity_id', '=', 'f.opportunity_id')
           ->on('lf.last_contact_date', '=', 'f.contact_date');
    })
    ->select('f.opportunity_id', 'f.next_contact_date');

  $overdueCount = DB::table('opportunities as o')
    ->leftJoinSub($lastFollowupData, 'lfd', function ($join) {
      $join->on('lfd.opportunity_id', '=', 'o.id');
    })
    ->where('o.assigned_user_id', $userId)
    ->whereNotNull('lfd.next_contact_date')
    ->whereDate('lfd.next_contact_date', '<', $today)
    ->count();

  // ---- Helper para ítems
  $navItemClass = function(string $pattern, bool $exactRoute = false, ?string $routeName = null) use ($pattern, $exactRoute, $routeName) {
    $active = $routeName
      ? request()->routeIs($routeName)
      : request()->is($pattern . '*');

    $base = 'flex items-center justify-between gap-3 px-3 py-2 rounded-xl text-sm font-medium transition';
    return $active
      ? $base . ' bg-gray-900 text-white'
      : $base . ' text-gray-700 hover:bg-gray-100';
  };

  $iconClass = 'w-5 h-5 shrink-0';
@endphp

<aside class="h-full w-72 bg-white ring-1 ring-gray-100 flex flex-col">
  <div class="p-5 border-b border-gray-100">
    <div class="text-lg font-semibold text-gray-900">Mini CRM</div>
    <div class="text-sm text-gray-500 mt-1">Panel comercial</div>
  </div>

  <nav class="p-4 space-y-1 flex-1">
    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}" class="{{ $navItemClass('dashboard', true, 'dashboard') }}">
      <div class="flex items-center gap-3">
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M4.5 10.5V21h5.25v-6h4.5v6H19.5V10.5" />
        </svg>
        <span>Dashboard</span>
      </div>
    </a>

    {{-- Agenda + badge atrasos --}}
    <a href="{{ route('agenda.index') }}" class="{{ $navItemClass('agenda', false) }}">
      <div class="flex items-center gap-3">
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M4 9h16M6 5h12a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2z" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 13h4m-4 4h8" />
        </svg>
        <span>Agenda</span>
      </div>

      @if($overdueCount > 0)
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-red-200">
          {{ $overdueCount }}
        </span>
      @else
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 ring-1 ring-green-200">
          0
        </span>
      @endif
    </a>

    {{-- Contactos --}}
    <a href="{{ route('contacts.index') }}" class="{{ $navItemClass('contacts', false) }}">
      <div class="flex items-center gap-3">
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.004 9.004 0 01-6 0M18 20a2 2 0 002-2v-1a4 4 0 00-4-4h-1m-6 0H8a4 4 0 00-4 4v1a2 2 0 002 2m10-11a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <span>Contactos</span>
      </div>
    </a>

    {{-- Oportunidades --}}
    <a href="{{ route('opportunities.index') }}" class="{{ $navItemClass('opportunities', false) }}">
      <div class="flex items-center gap-3">
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10M7 11h10M7 15h6M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
        </svg>
        <span>Oportunidades</span>
      </div>
    </a>

    {{-- Reporte --}}
    @can('reports.view')
      <a href="{{ route('reports.commercial') }}" class="{{ $navItemClass('reports/commercial', false) }}">
        <div class="flex items-center gap-3">
          <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 19V5a2 2 0 012-2h12a2 2 0 012 2v14" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 17V9m4 8V7m4 10v-5" />
          </svg>
          <span>Reporte comercial</span>
        </div>
      </a>
    @endcan
  </nav>

  <div class="p-4 border-t border-gray-100">
    <div class="text-xs text-gray-500">Usuario</div>
    <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? '' }}</div>

    <form method="POST" action="{{ route('logout') }}" class="mt-3">
      @csrf
      <button class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition">
        <svg class="{{ $iconClass }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
          <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H9m0 0l3-3m-3 3l3 3" />
        </svg>
        <span>Cerrar sesión</span>
      </button>
    </form>
  </div>
</aside>
