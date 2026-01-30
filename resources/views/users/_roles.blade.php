@php
  $isEdit = isset($user);

  $suggested = $defaultRole ?? 'Ejecutivo';

  $selected = old('roles', $isEdit
    ? ($user->roles?->pluck('name')->toArray() ?? [])
    : [$suggested]
  );
@endphp

<div class="mt-4">
  <div class="flex items-center justify-between">
    <div>
      <div class="text-sm font-semibold text-gray-900">Roles</div>
      <div class="text-xs text-gray-500">Definí el acceso del usuario.</div>
    </div>
  </div>

  @if(auth()->user()->can('roles.update'))
    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
      @foreach($roles as $role)
        <label class="flex items-center gap-2 rounded-xl ring-1 ring-gray-100 px-3 py-2 hover:bg-gray-50">
          <input type="checkbox"
                 name="roles[]"
                 value="{{ $role->name }}"
                 class="rounded"
                 @checked(in_array($role->name, $selected))>
          <span class="text-sm text-gray-800">{{ $role->name }}</span>
        </label>
      @endforeach
    </div>

    @error('roles')
      <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
    @enderror
  @else
    <div class="mt-3 rounded-xl ring-1 ring-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-700">
      No tenés permisos para modificar roles.
    </div>
  @endif
</div>
