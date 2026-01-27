<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Dashboard</h2>
        <div class="text-sm text-gray-500 mt-1">Resumen de tu gestión comercial.</div>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('opportunities.create') }}"><x-primary-button>Nueva oportunidad</x-primary-button></a>
        <a href="{{ route('contacts.create') }}"><x-secondary-button type="button">Nuevo contacto</x-secondary-button></a>
      </div>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

      {{-- KPIs --}}
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white p-4 ring-1 ring-gray-100">
          <div class="text-xs text-gray-500">Contactos</div>
          <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $contactsTotal }}</div>
          <div class="mt-2 text-sm text-gray-600">Total en el sistema</div>
        </div>

        <div class="rounded-2xl bg-white p-4 ring-1 ring-gray-100">
          <div class="text-xs text-gray-500">Mis oportunidades</div>
          <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $myOppTotal }}</div>
          <div class="mt-2 text-sm text-gray-600">Asignadas a mí</div>
        </div>

        <div class="rounded-2xl bg-white p-4 ring-1 ring-gray-100">
          <div class="text-xs text-gray-500">Abiertas</div>
          <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $myOppOpen }}</div>
          <div class="mt-2 text-sm text-gray-600">Sin cerrar</div>
        </div>

        <div class="rounded-2xl bg-white p-4 ring-1 ring-gray-100">
          <div class="text-xs text-gray-500">En cotización</div>
          <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $myOppInQuote }}</div>
          <div class="mt-2 text-sm text-gray-600">Requieren propuesta</div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Atrasados --}}
        <div class="lg:col-span-2 rounded-2xl bg-white p-5 ring-1 ring-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-lg font-semibold text-gray-900">Atrasados</div>
              <div class="text-sm text-gray-500 mt-1">Seguimientos con fecha vencida.</div>
            </div>

            <div class="inline-flex items-center gap-2">
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 ring-1 ring-red-200">
                {{ $overdueCount }}
              </span>
              <a href="{{ route('agenda.index') }}" class="text-sm font-medium text-gray-900 underline underline-offset-4">
                Ver agenda
              </a>
            </div>
          </div>

          <div class="mt-4 divide-y divide-gray-100">
            @forelse($overdue as $row)
              @php
                $label = trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? ''));
                $company = $row->company_name ? ' — ' . $row->company_name : '';
              @endphp
              <div class="py-3 flex items-start justify-between gap-4">
                <div>
                  <div class="font-medium text-gray-900">
                    #{{ $row->opportunity_id }} · {{ $row->detail }}
                  </div>
                  <div class="text-sm text-gray-600 mt-1">
                    {{ $label }}{{ $company }}
                  </div>
                  <div class="text-xs text-gray-500 mt-1">
                    Próximo contacto: {{ \Illuminate\Support\Carbon::parse($row->next_contact_date)->format('d/m/Y') }}
                  </div>
                </div>

                <div class="shrink-0 flex items-center gap-2">
                  <x-badge variant="default">{{ $row->status }}</x-badge>
                  <a href="{{ route('opportunities.edit', $row->opportunity_id) }}"
                     class="text-sm font-medium text-gray-900 underline underline-offset-4">
                    Abrir
                  </a>
                </div>
              </div>
            @empty
              <div class="py-6 text-sm text-gray-500">
                No tenés seguimientos atrasados 🎉
              </div>
            @endforelse
          </div>
        </div>

        {{-- Agenda Hoy --}}
        <div class="rounded-2xl bg-white p-5 ring-1 ring-gray-100">
          <div>
            <div class="text-lg font-semibold text-gray-900">Agenda de hoy</div>
            <div class="text-sm text-gray-500 mt-1">Contactos planificados para hoy.</div>
          </div>

          <div class="mt-4 space-y-3">
            @forelse($todayAgenda as $row)
              @php
                $label = trim(($row->last_name ?? '') . ', ' . ($row->first_name ?? ''));
                $company = $row->company_name ? ' — ' . $row->company_name : '';
              @endphp

              <div class="rounded-xl ring-1 ring-gray-100 p-3 hover:bg-gray-50/70 transition">
                <div class="flex items-start justify-between gap-3">
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      #{{ $row->opportunity_id }} · {{ $row->detail }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ $label }}{{ $company }}</div>
                  </div>
                  <x-badge variant="info">{{ $row->status }}</x-badge>
                </div>

                <div class="mt-2 flex justify-end">
                  <a href="{{ route('agenda.index') }}" class="text-sm font-medium text-gray-900 underline underline-offset-4">
                    Ir a agenda
                  </a>
                </div>
              </div>
            @empty
              <div class="text-sm text-gray-500">No tenés contactos programados para hoy.</div>
            @endforelse
          </div>
        </div>

      </div>

      {{-- Pipeline --}}
      <div class="rounded-2xl bg-white p-5 ring-1 ring-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-lg font-semibold text-gray-900">Pipeline</div>
            <div class="text-sm text-gray-500 mt-1">Cantidad de oportunidades por estado.</div>
          </div>
          <a href="{{ route('opportunities.index') }}" class="text-sm font-medium text-gray-900 underline underline-offset-4">
            Ver oportunidades
          </a>
        </div>

        @php
          $states = ['prospecto', 'cotizacion', 'ganada', 'rechazada', 'perdida'];
        @endphp

        <div class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-3">
          @foreach($states as $st)
            @php $qty = $pipeline[$st]->qty ?? 0; @endphp
            <div class="rounded-xl ring-1 ring-gray-100 p-4">
              <div class="text-xs text-gray-500 uppercase tracking-wider">{{ $st }}</div>
              <div class="mt-1 text-2xl font-semibold text-gray-900">{{ $qty }}</div>
            </div>
          @endforeach
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
