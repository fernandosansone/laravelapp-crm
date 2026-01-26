<x-app-layout>
  <x-slot name="header">
    <div>
      <h2 class="font-semibold text-xl text-gray-900 leading-tight">Nueva oportunidad</h2>
      <div class="text-sm text-gray-500 mt-1">Cargá una oportunidad y asignala automáticamente a tu usuario.</div>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

      @if ($errors->any())
        <x-card>
          <div class="p-3 bg-red-50 border border-red-100 rounded-xl text-red-800">
            Revisá los campos marcados en rojo.
          </div>
        </x-card>
      @endif

      <x-card title="Datos de la oportunidad" subtitle="Elegí un contacto, detalle y estado.">
        <form method="POST" action="{{ route('opportunities.store') }}">
          @include('opportunities._form', ['showClosedAt' => false])
        </form>
      </x-card>

    </div>
  </div>
</x-app-layout>
