<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Contactos</h2>
        <div class="text-sm text-gray-500 mt-1">Gestioná tu cartera y datos de contacto.</div>
      </div>

      @can('contacts.create')
        <a href="{{ route('contacts.create') }}">
          <x-primary-button>Nuevo contacto</x-primary-button>
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

      <x-card title="Búsqueda" subtitle="Encontrá contactos por nombre, empresa, email o teléfono.">
        <form method="GET">
          <x-filterbar>
            <div class="w-full md:w-96">
              <label class="block text-sm text-gray-600 mb-1">Buscar</label>
              <input type="text" name="q" value="{{ $q }}"
                     placeholder="Ej: Juan, ACME, mail@..."
                     class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
            </div>

            <div>
              <x-primary-button>Filtrar</x-primary-button>
            </div>

            @if($q)
              <div>
                <a href="{{ route('contacts.index') }}"><x-secondary-button type="button">Limpiar</x-secondary-button></a>
              </div>
            @endif
          </x-filterbar>
        </form>
      </x-card>

      <x-card title="Listado" subtitle="Mostrando {{ $contacts->total() }} contacto(s).">
        <x-table>
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Empresa</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Teléfono</th>
              <th class="text-left p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
              <th class="text-right p-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-100 bg-white">
            @foreach ($contacts as $c)
              <tr class="hover:bg-gray-50/60">
                <td class="p-3">
                  <div class="font-medium text-gray-900">{{ $c->last_name }}, {{ $c->first_name }}</div>
                  <div class="text-xs text-gray-500">ID #{{ $c->id }}</div>
                </td>
                <td class="p-3 text-gray-700">{{ $c->company_name ?? '—' }}</td>
                <td class="p-3 text-gray-700">{{ $c->phone_1 ?? '—' }}</td>
                <td class="p-3 text-gray-700">{{ $c->email_1 ?? '—' }}</td>
                <td class="p-3 text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-2">
                    @can('contacts.update')
                      <a href="{{ route('contacts.edit', $c) }}" class="text-sm font-medium text-gray-900 underline underline-offset-4">
                        Editar
                      </a>
                    @endcan

                    @can('contacts.delete')
                      <form method="POST" action="{{ route('contacts.destroy', $c) }}" onsubmit="return confirm('¿Eliminar contacto?');">
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
          {{ $contacts->links() }}
        </div>
      </x-card>

    </div>
  </div>
</x-app-layout>
