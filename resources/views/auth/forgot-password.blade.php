<x-guest-layout>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-gray-900">Recuperar contraseña</h1>
      <p class="text-sm text-gray-500 mt-1">
        Ingresá tu email y te enviaremos un enlace para restablecerla.
      </p>
    </div>

    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Error general (primer error) --}}
    @if ($errors->any())
      <div class="p-3 border rounded-xl bg-red-50 border-red-100 text-red-800 text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
      @csrf

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

      <div class="pt-2">
        <button type="submit"
                class="w-full inline-flex justify-center items-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900/30">
          Enviar enlace de recuperación
        </button>
      </div>

      <div class="text-sm text-gray-600">
        <a href="{{ route('login') }}" class="font-medium text-gray-900 underline underline-offset-4 hover:text-gray-700">
          Volver al login
        </a>
      </div>
    </form>
  </div>
</x-guest-layout>
