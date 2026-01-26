@php
  $input = "w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20";
  $error = "ring-red-300 focus:ring-red-300";
@endphp

@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

  {{-- CONTACTO + QUICK CREATE --}}
  <div class="md:col-span-2" x-data="contactQuickCreate()">
    <div class="flex items-center justify-between mb-1">
      <label class="block text-sm text-gray-600">Contacto</label>
      <button type="button" class="text-sm font-medium text-gray-900 underline underline-offset-4" @click="open = true">
        + Nuevo contacto
      </button>
    </div>

    <select x-ref="contactSelect" name="contact_id"
            class="{{ $input }} @error('contact_id') {{ $error }} @enderror">
      <option value="">Seleccionar...</option>
      @foreach($contacts as $c)
        @php
          $label = $c->last_name . ', ' . $c->first_name;
          if ($c->company_name) $label .= ' — ' . $c->company_name;
        @endphp
        <option value="{{ $c->id }}" @selected((string)old('contact_id', $opportunity->contact_id ?? '') === (string)$c->id)>
          {{ $label }}
        </option>
      @endforeach
    </select>
    @error('contact_id') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror

    {{-- MODAL NUEVO CONTACTO --}}
    <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black/50" style="display:none">
      <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg ring-1 ring-gray-100">
        <div class="px-6 pt-5 pb-4 border-b border-gray-100 flex justify-between items-center">
          <div>
            <div class="text-lg font-semibold text-gray-900">Nuevo contacto</div>
            <div class="text-sm text-gray-500 mt-1">Se agrega y se selecciona automáticamente.</div>
          </div>
          <button type="button" class="text-gray-500 hover:text-gray-900" @click="open=false">✕</button>
        </div>

        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-sm text-gray-600 mb-1">Nombre</label>
              <input x-model="form.first_name" class="{{ $input }}">
            </div>
            <div>
              <label class="block text-sm text-gray-600 mb-1">Apellido</label>
              <input x-model="form.last_name" class="{{ $input }}">
            </div>
            <div class="md:col-span-2">
              <label class="block text-sm text-gray-600 mb-1">Razón Social</label>
              <input x-model="form.company_name" class="{{ $input }}">
            </div>
            <div>
              <label class="block text-sm text-gray-600 mb-1">Email</label>
              <input x-model="form.email_1" class="{{ $input }}">
            </div>
            <div>
              <label class="block text-sm text-gray-600 mb-1">Teléfono</label>
              <input x-model="form.phone_1" class="{{ $input }}">
            </div>
          </div>

          <template x-if="error">
            <div class="mt-3 text-red-600 text-sm" x-text="error"></div>
          </template>

          <div class="mt-6 flex justify-end gap-2">
            <x-secondary-button type="button" @click="open=false">Cancelar</x-secondary-button>
            <x-primary-button type="button" @click="submit()">Crear</x-primary-button>
          </div>
        </div>
      </div>
    </div>

  </div>

  {{-- DETALLE --}}
  <div class="md:col-span-2">
    <label class="block text-sm text-gray-600 mb-1">Producto / Detalle</label>
    <input name="detail" value="{{ old('detail', $opportunity->detail ?? '') }}"
           class="{{ $input }} @error('detail') {{ $error }} @enderror">
    @error('detail') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
  </div>

  {{-- CANTIDAD --}}
  <div>
    <label class="block text-sm text-gray-600 mb-1">Cantidad</label>
    <input name="quantity" value="{{ old('quantity', $opportunity->quantity ?? '') }}"
           class="{{ $input }} @error('quantity') {{ $error }} @enderror">
    @error('quantity') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
  </div>

  {{-- MEDIDA (ENUM) --}}
  <div>
    <label class="block text-sm text-gray-600 mb-1">Medida</label>
    <select name="unit" class="{{ $input }} @error('unit') {{ $error }} @enderror">
      <option value="">(sin medida)</option>
      @foreach(\App\Enums\UnitOfMeasure::values() as $u)
        <option value="{{ $u }}" @selected(old('unit', $opportunity->unit ?? '') === $u)>{{ $u }}</option>
      @endforeach
    </select>
    @error('unit') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
  </div>

  {{-- IMPORTE con display + hidden --}}
  <div>
    <label class="block text-sm text-gray-600 mb-1">Importe</label>

    <div x-data="moneyInput('{{ old('amount', $opportunity->amount ?? '') }}')" x-init="init()">
      <input name="amount_display"
             x-model="display"
             @input="format()"
             @blur="format()"
             type="text"
             inputmode="decimal"
             class="{{ $input }} @error('amount') {{ $error }} @enderror"
             placeholder="1.234,56">

      <input type="hidden" name="amount" :value="raw">
    </div>

    @error('amount') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
  </div>

  {{-- ESTADO --}}
  <div>
    <label class="block text-sm text-gray-600 mb-1">Estado</label>
    <select name="status" class="{{ $input }} @error('status') {{ $error }} @enderror">
      @foreach($statuses as $st)
        <option value="{{ $st }}" @selected(old('status', ($opportunity->status->value ?? $opportunity->status ?? 'prospecto')) === $st)>
          {{ $st }}
        </option>
      @endforeach
    </select>
    @error('status') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
  </div>

  {{-- FECHA APERTURA --}}
  <div>
    <label class="block text-sm text-gray-600 mb-1">Fecha apertura</label>
    <input type="date" name="opened_at"
           value="{{ old('opened_at', ($opportunity->opened_at ?? now()->toDateString())) }}"
           class="{{ $input }} @error('opened_at') {{ $error }} @enderror">
    @error('opened_at') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
  </div>

  {{-- FECHA CIERRE (solo editar) --}}
  @if(($showClosedAt ?? false) === true)
    <div>
      <label class="block text-sm text-gray-600 mb-1">Fecha cierre</label>
      <input type="date" name="closed_at"
             value="{{ old('closed_at', $opportunity->closed_at ?? '') }}"
             class="{{ $input }} @error('closed_at') {{ $error }} @enderror">
      @error('closed_at') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
    </div>
  @endif

</div>

<div class="mt-6 flex justify-end gap-2">
  <a href="{{ route('opportunities.index') }}">
    <x-secondary-button type="button">Cancelar</x-secondary-button>
  </a>
  <x-primary-button>Guardar</x-primary-button>
</div>

{{-- JS: moneyInput + contactQuickCreate --}}
<script>
function moneyInput(initialRaw) {
  return {
    display: '',
    raw: (initialRaw || '').toString(),
    init() {
      if (this.raw) {
        const parts = this.raw.split('.');
        const int = parts[0] || '';
        const dec = (parts[1] || '').slice(0,2);
        const withDots = int.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        this.display = dec ? `${withDots},${dec}` : withDots;
      }
    },
    format() {
      let v = (this.display || '').toString().replace(/[^\d,]/g, '');
      const parts = v.split(',');
      let int = parts[0] || '';
      let dec = (parts[1] || '').slice(0,2);

      int = int.replace(/^0+(?=\d)/,'');
      const withDots = int.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

      this.display = dec.length ? `${withDots},${dec}` : withDots;
      this.raw = dec.length ? `${int}.${dec}` : int;
    }
  }
}

function contactQuickCreate() {
  return {
    open: false,
    error: '',
    form: { first_name:'', last_name:'', company_name:'', email_1:'', phone_1:'' },

    async submit() {
      this.error = '';
      try {
        const res = await fetch('{{ route('contacts.quick-store') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          },
          body: JSON.stringify(this.form)
        });

        if (!res.ok) {
          const payload = await res.json().catch(() => ({}));
          this.error = payload?.message ?? 'No se pudo crear el contacto.';
          return;
        }

        const data = await res.json();
        const opt = document.createElement('option');
        opt.value = data.id;
        opt.textContent = data.label;
        opt.selected = true;

        this.$refs.contactSelect.appendChild(opt);
        this.open = false;

        this.form = { first_name:'', last_name:'', company_name:'', email_1:'', phone_1:'' };
      } catch (e) {
        this.error = 'Error de red al crear el contacto.';
      }
    }
  }
}
</script>
