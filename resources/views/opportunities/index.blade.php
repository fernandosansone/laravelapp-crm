<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Oportunidades</h2>
        <div class="text-sm text-gray-500 mt-1">Seguimiento y pipeline comercial.</div>
      </div>

      @can('opportunities.create')
        <a href="{{ route('opportunities.create') }}">
          <x-primary-button>Nueva oportunidad</x-primary-button>
        </a>
      @endcan
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

      @if (session('success'))
        <x-card>
          <div class="p-3 bg-green-50 border border-green-100 rounded-xl text-green-800">
            {{ session('success') }}
          </div>
        </x-card>
      @endif

      <x-card title="Filtros" subtitle="Buscá por detalle o contacto y filtrá por estado.">
        <form method="GET">
          <x-filterbar>
            <div class="w-full md:w-[28rem]">
              <label class="block text-sm text-gray-600 mb-1">Buscar</label>
              <input type="text" name="q" value="{{ $q }}"
                     placeholder="Ej: Servicio X, Pérez, ACME..."
                     class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
            </div>

            <div class="w-full md:w-60">
              <label class="block text-sm text-gray-600 mb-1">Estado</label>
              <select name="status"
                      class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                <option value="">Todos</option>
                @foreach($statuses as $st)
                  <option value="{{ $st }}" @selected($status === $st)>{{ $st }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <x-primary-button>Filtrar</x-primary-button>
            </div>

            @if($q || $status)
              <div>
                <a href="{{ route('opportunities.index') }}">
                  <x-secondary-button type="button">Limpiar</x-secondary-button>
                </a>
              </div>
            @endif
          </x-filterbar>
        </form>
      </x-card>

      <x-card title="Listado" subtitle="Mostrando {{ $opportunities->total() }} oportunidad(es).">
        <x-table>
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Contacto</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Detalle</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Importe</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Ejecutivo</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100 bg-white">
            @foreach($opportunities as $o)
              @php
                $st = is_object($o->status) ? $o->status->value : $o->status;
                $variant = match($st) {
                  'prospecto' => 'info',
                  'cotizacion' => 'warning',
                  'ganada' => 'success',
                  'rechazada' => 'danger',
                  'perdida' => 'danger',
                  default => 'default',
                };

                $contactName = trim(($o->contact?->last_name ?? '') . ', ' . ($o->contact?->first_name ?? ''));
              @endphp

              <tr class="hover:bg-gray-50/60">
                <td class="p-3">
                  <div class="font-medium text-gray-900">#{{ $o->id }}</div>
                  <div class="text-xs text-gray-500">
                    {{ $o->opened_at ? \Illuminate\Support\Carbon::parse($o->opened_at)->format('d/m/Y') : '—' }}
                  </div>
                </td>

                <td class="p-3">
                  <div class="font-medium text-gray-900">{{ $contactName ?: '—' }}</div>
                  @if($o->contact?->company_name)
                    <div class="text-xs text-gray-500">{{ $o->contact->company_name }}</div>
                  @endif
                </td>

                <td class="p-3 text-gray-800">
                  <div class="font-medium">{{ $o->detail }}</div>
                  <div class="text-xs text-gray-500">
                    {{ $o->quantity ?? '—' }} {{ $o->unit ?? '' }}
                  </div>
                </td>

                <td class="p-3">
                  <x-badge :variant="$variant">{{ $st }}</x-badge>
                </td>

                <td class="p-3 text-gray-800">
                  {{ $o->amount !== null ? number_format((float)$o->amount, 2, ',', '.') : '—' }}
                </td>

                <td class="p-3 text-gray-700">
                  {{ $o->assignedUser?->name ?? '—' }}
                </td>

                <td class="p-3 text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-2">
                    @can('opportunities.update')
                      <a href="{{ route('opportunities.edit', $o) }}"
                         class="text-sm font-medium text-gray-900 underline underline-offset-4">
                        Editar
                      </a>
                    @endcan

                    @can('opportunities.delete')
                      <form method="POST" action="{{ route('opportunities.destroy', $o) }}"
                            onsubmit="return confirm('¿Eliminar oportunidad?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-sm font-medium text-red-600 underline underline-offset-4">
                          Eliminar
                        </button>
                      </form>
                    @endcan
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </x-table>

        <div class="mt-4">
          {{ $opportunities->links() }}
        </div>
      </x-card>

    </div>
  </div>
</x-app-layout>
