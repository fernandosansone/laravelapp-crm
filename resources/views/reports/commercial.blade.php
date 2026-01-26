<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reporte de Gestión Comercial</h2>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

      {{-- FILTROS --}}
      <div class="bg-white shadow rounded p-4 mb-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
          <div>
            <label class="block text-sm mb-1">Desde</label>
            <input type="date" name="from" value="{{ $from }}" class="border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm mb-1">Hasta</label>
            <input type="date" name="to" value="{{ $to }}" class="border rounded px-3 py-2">
          </div>
          <div>
            <label class="block text-sm mb-1">Estado</label>
            <select name="status" class="border rounded px-3 py-2">
              <option value="">Todos</option>
              @foreach($statuses as $st)
                <option value="{{ $st }}" @selected($status===$st)>{{ $st }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Vendedor</label>
            <select name="seller_id" class="border rounded px-3 py-2">
              <option value="">Todos</option>
              @foreach($sellers as $s)
                <option value="{{ $s->id }}" @selected((string)$sellerId===(string)$s->id)>{{ $s->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="grow">
            <label class="block text-sm mb-1">Buscar</label>
            <input type="text" name="q" value="{{ $q }}" placeholder="Contacto / empresa / detalle" class="border rounded px-3 py-2 w-full">
          </div>
          <div class="flex items-center gap-2">
            <input type="checkbox" name="only_overdue" value="1" @checked($onlyOverdue)>
            <span class="text-sm">Solo atrasadas</span>
          </div>
          <x-primary-button>Aplicar</x-primary-button>
        </form>
      </div>

      {{-- KPIs --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div class="bg-white shadow rounded p-4">
          <div class="text-sm text-gray-500">Total oportunidades</div>
          <div class="text-2xl font-semibold">{{ $total }}</div>
        </div>
        <div class="bg-white shadow rounded p-4">
          <div class="text-sm text-gray-500">Con próxima fecha</div>
          <div class="text-2xl font-semibold">{{ $withNext }}</div>
        </div>
        <div class="bg-white shadow rounded p-4">
          <div class="text-sm text-gray-500">Atrasadas</div>
          <div class="text-2xl font-semibold">{{ $overdue }}</div>
        </div>
      </div>

      {{-- RESUMEN POR ESTADO --}}
      <div class="bg-white shadow rounded p-4 mb-4">
        <div class="font-semibold mb-2">Distribución por estado</div>
        <div class="flex flex-wrap gap-2">
          @foreach($byStatus as $st => $cnt)
            <span class="px-2 py-1 border rounded text-sm">{{ $st }}: <strong>{{ $cnt }}</strong></span>
          @endforeach
        </div>
      </div>

      {{-- RESUMEN POR VENDEDOR --}}
      <div class="bg-white shadow rounded p-4 mb-4">
        <div class="font-semibold mb-2">Resumen por vendedor</div>
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="border-b">
                <th class="text-left p-2">Vendedor</th>
                <th class="text-right p-2">Total</th>
                <th class="text-right p-2">Atrasadas</th>
                <th class="text-right p-2">Ganadas</th>
                <th class="text-right p-2">Perdidas</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bySeller as $name => $d)
                <tr class="border-b">
                  <td class="p-2">{{ $name }}</td>
                  <td class="p-2 text-right">{{ $d['total'] }}</td>
                  <td class="p-2 text-right">{{ $d['overdue'] }}</td>
                  <td class="p-2 text-right">{{ $d['ganadas'] }}</td>
                  <td class="p-2 text-right">{{ $d['perdidas'] }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- TABLA DETALLE --}}
      <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="border-b">
              <th class="text-left p-3">#</th>
              <th class="text-left p-3">Vendedor</th>
              <th class="text-left p-3">Contacto</th>
              <th class="text-left p-3">Empresa</th>
              <th class="text-left p-3">Detalle</th>
              <th class="text-left p-3">Estado</th>
              <th class="text-left p-3">Próximo contacto</th>
              <th class="text-left p-3">Último contacto</th>
              <th class="text-left p-3">Atraso (días)</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $r)
              @php
                $next = $r->next_contact_date ? \Illuminate\Support\Carbon::parse($r->next_contact_date) : null;
                $late = $next && $next->isPast() ? $next->startOfDay()->diffInDays(now()->startOfDay()) : 0;
              @endphp
              <tr class="border-b">
                <td class="p-3">{{ $r->id }}</td>
                <td class="p-3">{{ $r->seller_name }}</td>
                <td class="p-3">{{ $r->c_last_name }}, {{ $r->c_first_name }}</td>
                <td class="p-3">{{ $r->c_company_name }}</td>
                <td class="p-3">{{ $r->detail }}</td>
                <td class="p-3">{{ $r->status }}</td>
                <td class="p-3">{{ $r->next_contact_date }}</td>
                <td class="p-3">{{ $r->last_contact_date }}</td>
                <td class="p-3">
                  @if($next && $late > 0)
                    <span class="px-2 py-1 rounded bg-red-100 border border-red-200">{{ $late }}</span>
                  @elseif($next)
                    <span class="px-2 py-1 rounded bg-green-100 border border-green-200">0</span>
                  @else
                    <span class="text-gray-500">—</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div class="p-3">
          {{ $rows->links() }}
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
