@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    <!--<div class="md:col-span-2">
        <label class="block mb-1">Contacto</label>
        <select name="contact_id" class="border rounded w-full px-3 py-2">
            <option value="">Seleccionar...</option>
            @foreach($contacts as $c)
                @php
                    $label = $c->last_name . ', ' . $c->first_name;
                    if ($c->company_name) $label .= ' — ' . $c->company_name;
                @endphp
                <option value="{{ $c->id }}" @selected((string)old('contact_id', $opportunity->contact_id ?? '') === (string)$c->id)>{{ $label }}</option>
            @endforeach
        </select>
        @error('contact_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>-->

    <div class="md:col-span-2" x-data="contactQuickCreate()">
        <div class="flex items-center justify-between mb-1">
            <label class="block">Contacto</label>

            <button type="button" class="underline" @click="open = true">
            + Nuevo contacto
            </button>
        </div>

        <select x-ref="contactSelect" name="contact_id" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
            <option value="">Seleccionar...</option>
            @foreach($contacts as $c)
            @php
                $label = $c->last_name . ', ' . $c->first_name;
                if ($c->company_name) $label .= ' — ' . $c->company_name;
            @endphp
            <option value="{{ $c->id }}" @selected((string)old('contact_id', $opportunity->contact_id ?? '') === (string)$c->id)>{{ $label }}</option>
            @endforeach
        </select>
        @error('contact_id') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror

        <!-- MODAL -->
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black/50" style="display:none">
            <div class="bg-white rounded p-6 w-full max-w-lg">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Nuevo contacto</h3>
                    <button type="button" @click="open=false">✕</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block mb-1">Nombre</label>
                        <input x-model="form.first_name" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                    </div>
                    <div>
                        <label class="block mb-1">Apellido</label>
                        <input x-model="form.last_name" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block mb-1">Razón Social</label>
                        <input x-model="form.company_name" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                    </div>
                    <div>
                        <label class="block mb-1">Email</label>
                        <input x-model="form.email_1" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                    </div>
                    <div>
                        <label class="block mb-1">Teléfono</label>
                        <input x-model="form.phone_1" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
                    </div>
                </div>

                <div class="mt-4 flex gap-2 justify-end">
                    <x-secondary-button type="button" @click="open=false">Cancelar</x-secondary-button>
                    <x-primary-button type="button" @click="submit()">Crear</x-primary-button>
                </div>

                <template x-if="error">
                    <div class="mt-3 text-red-600 text-sm" x-text="error"></div>
                </template>
            </div>
        </div>
    </div>

    <script>
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

                // reset
                this.form = { first_name:'', last_name:'', company_name:'', email_1:'', phone_1:'' };

                } catch (e) {
                    this.error = 'Error de red al crear el contacto.';
                }
            }
        }
    }
    </script>

    <div class="md:col-span-2">
        <label class="block mb-1">Producto / Detalle</label>
        <input name="detail" value="{{ old('detail', $opportunity->detail ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('detail') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Cantidad</label>
        <input name="quantity" value="{{ old('quantity', $opportunity->quantity ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('quantity') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Medida</label>
        <!--<input name="unit" value="{{ old('unit', $opportunity->unit ?? '') }}" class="border rounded w-full px-3 py-2">-->
        <select name="unit" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
            <option value="">(sin medida)</option>
            @foreach(\App\Enums\UnitOfMeasure::values() as $u)
                <option value="{{ $u }}" @selected(old('unit', $opportunity->unit ?? '') === $u)>{{ $u }}</option>
            @endforeach
        </select>
        @error('unit') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Importe</label>
        <!--<input name="amount" value="{{ old('amount', $opportunity->amount ?? '') }}" class="border rounded w-full px-3 py-2">-->
        <input name="amount_display"
            x-data="moneyInput('{{ old('amount', $opportunity->amount ?? '') }}')"
            x-model="display"
            @input="format()"
            @blur="format()"
            type="text"
            inputmode="decimal"
            class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        <input type="hidden" name="amount" :value="raw">
        @error('amount') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <script>
        function moneyInput(initialRaw) {
            return {
                display: '',
                raw: (initialRaw || '').toString(),
                init() {
                    // inicializa display desde raw si viene 1234.56
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
    </script>
    
    <div>
        <label class="block mb-1">Estado</label>
        <select name="status" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
            @foreach($statuses as $st)
                <option value="{{ $st }}" @selected(old('status', ($opportunity->status->value ?? $opportunity->status ?? 'prospecto')) === $st)>{{ $st }}</option>
            @endforeach
        </select>
        @error('status') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Fecha apertura</label>
        <!--<input type="date" name="opened_at" value="{{ old('opened_at', $opportunity->opened_at ?? '') }}" class="border rounded w-full px-3 py-2">-->
        <input type="date" name="opened_at"
            value="{{ old('opened_at', now()->toDateString()) }}"
            class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('opened_at') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    @if(($showClosedAt ?? false) === true)
    <div>
        <label class="block mb-1">Fecha cierre</label>
        <input type="date" name="closed_at" value="{{ old('closed_at', $opportunity->closed_at ?? '') }}"
            class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('closed_at') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
    @endif
</div>

<div class="mt-4 flex gap-2">
    <!--<button class="px-4 py-2 bg-black text-white rounded">Guardar</button>-->
    <x-primary-button>Guardar</x-primary-button>
    <a href="{{ route('opportunities.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
</div>
