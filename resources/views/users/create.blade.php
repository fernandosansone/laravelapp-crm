<x-app-layout>
  <x-slot name="header">
    <div>
      <h2 class="font-semibold text-xl text-gray-900 leading-tight">Nuevo usuario</h2>
      <div class="text-sm text-gray-500 mt-1">Crear cuenta y asignar roles.</div>
    </div>
  </x-slot>

  <div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

      @if ($errors->any())
        <x-card><x-alert type="error">Revisá los campos marcados.</x-alert></x-card>
      @endif

      <x-card title="Datos del usuario" subtitle="Nombre, email, contraseña y roles.">
        <form method="POST" action="{{ route('users.store') }}">
          @csrf
          @include('users._form', ['roles' => $roles, 'defaultRole' => $defaultRole])
        </form>
      </x-card>

    </div>
  </div>
</x-app-layout>
