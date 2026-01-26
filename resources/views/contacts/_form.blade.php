@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
        <label class="block mb-1">Nombre</label>
        <input name="first_name" value="{{ old('first_name', $contact->first_name ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('first_name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Apellido</label>
        <input name="last_name" value="{{ old('last_name', $contact->last_name ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('last_name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Teléfono 1</label>
        <input name="phone_1" value="{{ old('phone_1', $contact->phone_1 ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('phone_1') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Teléfono 2</label>
        <input name="phone_2" value="{{ old('phone_2', $contact->phone_2 ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('phone_2') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Mail 1</label>
        <input name="email_1" value="{{ old('email_1', $contact->email_1 ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('email_1') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div>
        <label class="block mb-1">Mail 2</label>
        <input name="email_2" value="{{ old('email_2', $contact->email_2 ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('email_2') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block mb-1">Razón Social</label>
        <input name="company_name" value="{{ old('company_name', $contact->company_name ?? '') }}" class="w-full bg-white rounded-xl px-3 py-2 ring-1 ring-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-900/20">
        @error('company_name') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-4 flex gap-2">
    <!--<button class="px-4 py-2 bg-black text-white rounded">Guardar</button>-->
    <x-primary-button>Guardar</x-primary-button>
    <a href="{{ route('contacts.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
</div>
