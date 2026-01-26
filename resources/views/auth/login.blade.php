<x-guest-layout>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Iniciar sesión</h1>
      <p class="text-sm text-gray-500 mt-1">Ingresá con tu usuario y contraseña.</p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Error general (primer error) --}}
    @if ($errors->any())
      <div class="p-3 border rounded-xl bg-red-50 border-red-100 text-red-800 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
      @csrf

      {{-- Email --}}
      <div>
        <label class="block text-sm text-gray-600 mb-1" for="email">Email</label>
        <input id="email"
               name="email"
               type="email"
               value="{{ old('email') }}"
               required
               autofocus
               autocomplete="username"
               placeholder="tu@email.com"
               class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('email')
          <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
      </div>

      {{-- Password --}}
      <div>
        <label class="block text-sm text-gray-600 mb-1" for="password">Contraseña</label>
        <input id="password"
               name="password"
               type="password"
               required
               autocomplete="current-password"
               placeholder="••••••••"
               class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('password')
          <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="flex items-center justify-between">
        {{-- Remember --}}
        <label class="inline-flex items-center gap-2 text-sm text-gray-700 select-none">
          <input type="checkbox"
                 name="remember"
                 class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-900/20">
          <span>Recordarme</span>
        </label>

        @if (Route::has('password.request'))
          <a class="text-sm font-medium text-gray-900 underline underline-offset-4 hover:text-gray-700"
             href="{{ route('password.request') }}">
            Olvidé mi contraseña
          </a>
        @endif
      </div>

      <div class="pt-2">
        <button type="submit"
                class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900/30">
          Entrar
        </button>
      </div>

      <div class="text-xs text-gray-500">
        Al ingresar aceptás las políticas internas de uso del sistema.
      </div>
    </form>
  </div>
</x-guest-layout>
