<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mini CRM') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen bg-gray-50">
      {{-- Fondo moderno --}}
      <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-gray-900/10 blur-3xl"></div>
        <div class="absolute top-20 -right-24 h-72 w-72 rounded-full bg-blue-600/10 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-amber-500/10 blur-3xl"></div>
      </div>

      <div class="min-h-screen flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-5xl">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

            {{-- Panel izquierdo (branding) --}}
            <div class="hidden lg:flex flex-col justify-between rounded-3xl bg-gray-900 text-white p-10 shadow-sm">
              <div>
                <div class="text-sm text-gray-300">Bienvenido a</div>
                <div class="text-3xl font-semibold tracking-tight mt-1">
                  {{ config('app.name', 'Mini CRM') }}
                </div>

                <div class="mt-6 space-y-3 text-gray-200">
                  <div class="flex gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10">✓</span>
                    <div>
                      <div class="font-medium">Agenda y atrasos</div>
                      <div class="text-sm text-gray-300">Seguimientos organizados por día.</div>
                    </div>
                  </div>

                  <div class="flex gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10">✓</span>
                    <div>
                      <div class="font-medium">Pipeline comercial</div>
                      <div class="text-sm text-gray-300">Prospecto → Cotización → Ganada/Perdida.</div>
                    </div>
                  </div>

                  <div class="flex gap-3">
                    <span class="mt-1 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-white/10">✓</span>
                    <div>
                      <div class="font-medium">Reportes</div>
                      <div class="text-sm text-gray-300">KPIs y seguimiento por vendedor.</div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="text-xs text-gray-400">
                © {{ date('Y') }} {{ config('app.name', 'Mini CRM') }} · Acceso seguro
              </div>
            </div>

            {{-- Panel derecho (form) --}}
            <div class="rounded-3xl bg-white p-8 sm:p-10 shadow-sm ring-1 ring-gray-100">
              <div class="mb-6">
                <a href="/" class="inline-flex items-center gap-2 text-gray-900">
                  <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-gray-900 text-white font-semibold">
                    CRM
                  </span>
                  <span class="font-semibold">{{ config('app.name', 'Mini CRM') }}</span>
                </a>
              </div>

              {{ $slot }}
            </div>

          </div>
        </div>
      </div>
    </div>
  </body>
</html>
