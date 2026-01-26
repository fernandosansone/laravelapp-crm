<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Reporte de Gestión Comercial</h2>
        <div class="text-sm text-gray-500 mt-1">KPIs, estado del pipeline y atrasos por vendedor.</div>
      </div>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

      {{-- FILTROS --}}
      <x-card title="Filtros" subtitle="Acotá el reporte por fechas, estado, vendedor y texto.">
        <form method="GET">
          <x-filterbar>
            <div>
              <label class="block text-sm text-gray-600 mb-1">Desde</label>
              <input type="date" name="from" value="{{ $from }}"
                     class="bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
            </div>

            <div>
              <label class="block text-sm text-gray-600 mb-1">Hasta</label>
              <input type="date" name="to" value="{{ $to }}"
                     class="bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
            </div>

            <div class="w-full md:w-56">
              <label class="block text-sm text-gray-600 mb-1">Estado</label>
              <select name="status"
                      class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                <option value="">Todos</option>
                @foreach($statuses as $st)
                  <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
                @endforeach
              </select>
            </div>

            <div class="w-full md:w-64">
              <label class="block text-sm text-gray-600 mb-1">Vendedor</label>
              <select name="seller_id"
                      class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                <option value="">Todos</option>
                @foreach($sellers as $s)
                  <option value="{{ $s->id }}" @selected((string)$sellerId === (string)$s->id)>{{ $s->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="grow w-full md:w-auto">
              <label class="block text-sm text-gray-600 mb-1">Buscar</label>
              <input type="text" name="q" value="{{ $q }}" placeholder="Contacto / empresa / detalle"
                     class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
            </div>

            <div class="flex items-center gap-2 pb-2">
              <input type="checkbox" name="only_overdue" value="1" @checked($onlyOverdue)>
              <span class="text-sm text-gray-700">Solo atrasadas</span>
            </div>

            <x-primary-button>Aplicar</x-primary-button>

            @if($from || $to || $status || $sellerId || $q || $onlyOverdue)
              <a href="{{ route('reports.commercial') }}">
                <x-secondary-button type="button">Limpiar</x-secondary-button>
              </a>
            @endif
          </x-filterbar>
        </form>
      </x-card>

      {{-- KPIs --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-card>
          <div class="text-sm text-gray-500">Total oportunidades</div>
          <div class="text-2xl font-semibold text-gray-900">{{ $total }}</div>
        </x-card>

        <x-card>
          <div class="text-sm text-gray-500">Con próxima fecha</div>
          <div class="text-2xl font-semibold text-gray-900">{{ $withNext }}</div>
        </x-card>

        <x-card>
          <div class="text-sm text-gray-500">Atrasadas</div>
          <div class="text-2xl font-semibold text-gray-900">{{ $overdue }}</div>
        </x-card>
      </div>

      {{-- ESTADOS --}}
      <x-card title="Distribución por estado" subtitle="Conteo de oportunidades por estado bajo los filtros actuales.">
        <div class="flex flex-wrap gap-2">
          @foreach($byStatus as $st => $cnt)
            @php
              $variant = match($st) {
                'prospecto' => 'info',
                'cotizacion' => 'warning',
                'ganada' => 'success',
                'rechazada' => 'danger',
                'perdida' => 'danger',
                default => 'default',
              };
            @endphp
            <x-badge :variant="$variant">{{ $st }}: <strong>{{ $cnt }}</strong></x-badge>
          @endforeach
        </div>
      </x-card>

      {{-- POR VENDEDOR --}}
      <x-card title="Resumen por vendedor" subtitle="Totales y atrasos. Útil para gestión.">
        <x-table>
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Vendedor</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Atrasadas</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Ganadas</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Perdidas</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            @foreach($bySeller as $name => $d)
              <tr class="hover:bg-gray-50/60">
                <td class="p-3 font-medium text-gray-900">{{ $name }}</td>
                <td class="p-3 text-right text-gray-800">{{ $d['total'] }}</td>
                <td class="p-3 text-right">
                  @if($d['overdue'] > 0)
                    <x-badge variant="danger">{{ $d['overdue'] }}</x-badge>
                  @else
                    <x-badge variant="success">0</x-badge>
                  @endif
                </td>
                <td class="p-3 text-right text-gray-800">{{ $d['ganadas'] }}</td>
                <td class="p-3 text-right text-gray-800">{{ $d['perdidas'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </x-table>
      </x-card>

      {{-- DETALLE --}}
      <x-card title="Detalle" subtitle="Listado con próximo contacto, último contacto y atraso (días).">
        <x-table>
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Vendedor</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Contacto</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Empresa</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Detalle</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Próximo</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Último</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Atraso</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100 bg-white">
            @foreach($rows as $r)
              @php
                $next = $r->next_contact_date ? \Illuminate\Support\Carbon::parse($r->next_contact_date) : null;
                $late = ($next && $next->isPast()) ? $next->startOfDay()->diffInDays(now()->startOfDay()) : 0;

                $st = $r->status;
                $variant = match($st) {
                  'prospecto' => 'info',
                  'cotizacion' => 'warning',
                  'ganada' => 'success',
                  'rechazada' => 'danger',
                  'perdida' => 'danger',
                  default => 'default',
                };
              @endphp

              <tr class="hover:bg-gray-50/60">
                <td class="p-3 text-gray-800">#{{ $r->id }}</td>
                <td class="p-3 text-gray-800">{{ $r->seller_name }}</td>
                <td class="p-3 text-gray-900 font-medium">{{ $r->c_last_name }}, {{ $r->c_first_name }}</td>
                <td class="p-3 text-gray-700">{{ $r->c_company_name ?? '—' }}</td>
                <td class="p-3 text-gray-800">{{ $r->detail }}</td>
                <td class="p-3"><x-badge :variant="$variant">{{ $st }}</x-badge></td>
                <td class="p-3 text-gray-700">{{ $r->next_contact_date ?? '—' }}</td>
                <td class="p-3 text-gray-700">{{ $r->last_contact_date ?? '—' }}</td>
                <td class="p-3">
                  @if($next && $late > 0)
                    <x-badge variant="danger">{{ $late }}</x-badge>
                  @elseif($next)
                    <x-badge variant="success">0</x-badge>
                  @else
                    <span class="text-gray-500">—</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </x-table>

        <div class="mt-4">
          {{ $rows->links() }}
        </div>
      </x-card>

    </div>
  </div>
</x-app-layout>
