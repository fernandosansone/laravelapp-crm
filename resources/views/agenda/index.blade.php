<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between gap-4">
      <div>
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Mi agenda</h2>
        <div class="text-sm text-gray-500 mt-1">Tus seguimientos del día y atrasos.</div>
      </div>

      <a href="{{ route('opportunities.index') }}">
        <x-secondary-button type="button">Ver oportunidades</x-secondary-button>
      </a>
    </div>
  </x-slot>

  <div class="py-6" x-data="followupModal()">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

      @if (session('success'))
        <x-card>
          <div class="p-3 bg-green-50 border border-green-100 rounded-xl text-green-800">
            {{ session('success') }}
          </div>
        </x-card>
      @endif

      {{-- KPIs rápidos --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-card>
          <div class="text-sm text-gray-500">Atrasados</div>
          <div class="text-2xl font-semibold text-gray-900">{{ count($overdue) }}</div>
        </x-card>
        <x-card>
          <div class="text-sm text-gray-500">Para hoy</div>
          <div class="text-2xl font-semibold text-gray-900">{{ count($todayRows) }}</div>
        </x-card>
        <x-card>
          <div class="text-sm text-gray-500">Sin fecha</div>
          <div class="text-2xl font-semibold text-gray-900">{{ count($noDate) }}</div>
        </x-card>
      </div>

      {{-- ATRASADOS --}}
      <x-card title="Atrasados" subtitle="Priorizá estos seguimientos: muestran días de atraso.">
        <x-table>
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Contacto</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Oportunidad</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Próximo</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Atraso</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($overdue as $r)
              @php $o = $r['opportunity']; @endphp
              <tr class="hover:bg-gray-50/60">
                <td class="p-3">
                  <div class="font-medium text-gray-900">
                    {{ $o->contact?->last_name }}, {{ $o->contact?->first_name }}
                  </div>
                  @if($o->contact?->company_name)
                    <div class="text-xs text-gray-500">{{ $o->contact->company_name }}</div>
                  @endif
                </td>
                <td class="p-3 text-gray-800">{{ $o->detail }}</td>
                <td class="p-3 text-gray-700">{{ $r['next_contact_date'] }}</td>
                <td class="p-3">
                  <x-badge variant="danger">{{ $r['days_late'] }} día(s)</x-badge>
                </td>
                <td class="p-3 text-right">
                  <x-primary-button type="button"
                    @click="open({{ $o->id }}, '{{ addslashes($o->detail) }}', '{{ $r['next_contact_date'] }}')">
                    Registrar seguimiento
                  </x-primary-button>
                </td>
              </tr>
            @empty
              <tr><td class="p-3 text-gray-500" colspan="5">Sin atrasos 🎉</td></tr>
            @endforelse
          </tbody>
        </x-table>
      </x-card>

      {{-- HOY --}}
      <x-card title="Para hoy" subtitle="Seguimientos programados para el día de hoy.">
        <x-table>
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Contacto</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Oportunidad</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Próximo</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($todayRows as $r)
              @php $o = $r['opportunity']; @endphp
              <tr class="hover:bg-gray-50/60">
                <td class="p-3">
                  <div class="font-medium text-gray-900">
                    {{ $o->contact?->last_name }}, {{ $o->contact?->first_name }}
                  </div>
                  @if($o->contact?->company_name)
                    <div class="text-xs text-gray-500">{{ $o->contact->company_name }}</div>
                  @endif
                </td>
                <td class="p-3 text-gray-800">{{ $o->detail }}</td>
                <td class="p-3 text-gray-700">{{ $r['next_contact_date'] }}</td>
                <td class="p-3 text-right">
                  <x-primary-button type="button"
                    @click="open({{ $o->id }}, '{{ addslashes($o->detail) }}', '{{ $r['next_contact_date'] }}')">
                    Registrar seguimiento
                  </x-primary-button>
                </td>
              </tr>
            @empty
              <tr><td class="p-3 text-gray-500" colspan="4">No hay tareas para hoy.</td></tr>
            @endforelse
          </tbody>
        </x-table>
      </x-card>

      {{-- SIN FECHA --}}
      <x-card title="Sin próxima fecha" subtitle="Oportunidades sin seguimiento programado (crear el primero).">
        <x-table>
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Contacto</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Oportunidad</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Acción</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($noDate as $r)
              @php $o = $r['opportunity']; @endphp
              <tr class="hover:bg-gray-50/60">
                <td class="p-3">
                  <div class="font-medium text-gray-900">
                    {{ $o->contact?->last_name }}, {{ $o->contact?->first_name }}
                  </div>
                  @if($o->contact?->company_name)
                    <div class="text-xs text-gray-500">{{ $o->contact->company_name }}</div>
                  @endif
                </td>
                <td class="p-3 text-gray-800">{{ $o->detail }}</td>
                <td class="p-3 text-right">
                  <x-primary-button type="button"
                    @click="open({{ $o->id }}, '{{ addslashes($o->detail) }}', '')">
                    Registrar primer seguimiento
                  </x-primary-button>
                </td>
              </tr>
            @empty
              <tr><td class="p-3 text-gray-500" colspan="3">No hay oportunidades sin fecha.</td></tr>
            @endforelse
          </tbody>
        </x-table>
      </x-card>

      {{-- MODAL --}}
      <div x-show="isOpen" class="fixed inset-0 flex items-center justify-center bg-black/50" style="display:none">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-xl ring-1 ring-gray-100">
          <div class="px-6 pt-5 pb-4 border-b border-gray-100 flex justify-between items-center">
            <div>
              <div class="text-lg font-semibold text-gray-900">Registrar seguimiento</div>
              <div class="text-sm text-gray-500 mt-1" x-text="oppDetail"></div>
            </div>
            <button type="button" class="text-gray-500 hover:text-gray-900" @click="isOpen=false">✕</button>
          </div>

          <div class="p-6">
            <form method="POST" action="{{ route('agenda.followups.store') }}">
              @csrf
              <input type="hidden" name="opportunity_id" :value="oppId">

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Fecha contacto</label>
                  <input type="datetime-local" name="contact_date"
                         value="{{ now()->format('Y-m-d\\TH:i') }}"
                         class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                </div>

                <div>
                  <label class="block text-sm text-gray-600 mb-1">Forma</label>
                  <select name="contact_method"
                          class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                    <option value="telefono">Teléfono</option>
                    <option value="email">Email</option>
                    <option value="reunion">Reunión</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="otro">Otro</option>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm text-gray-600 mb-1">Respuesta</label>
                  <textarea name="response" rows="3"
                            class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20"></textarea>
                </div>

                <div>
                  <label class="block text-sm text-gray-600 mb-1">Próximo contacto</label>
                  <input type="date" name="next_contact_date"
                         class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                </div>

                <div class="flex items-end">
                  <template x-if="dueDate">
                    <div class="text-sm text-gray-500">
                      Vencía: <span class="font-medium text-gray-900" x-text="dueDate"></span>
                    </div>
                  </template>
                </div>
              </div>

              <div class="mt-6 flex justify-end gap-2">
                <x-secondary-button type="button" @click="isOpen=false">Cancelar</x-secondary-button>
                <x-primary-button>Guardar</x-primary-button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script>
    function followupModal() {
      return {
        isOpen: false,
        oppId: null,
        oppDetail: '',
        dueDate: '',
        open(id, detail, due) {
          this.oppId = id;
          this.oppDetail = detail;
          this.dueDate = due || '';
          this.isOpen = true;
        }
      }
    }
  </script>
</x-app-layout>
