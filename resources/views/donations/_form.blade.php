@php
    $isEdit = $donation->exists;
    $expiryValue = old('expiry_time', $donation->expiry_time?->format('Y-m-d\TH:i'));
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="mb-2 block text-sm font-semibold">Food Category</label>
            <select name="food_category" class="w-full cursor-pointer appearance-none rounded-xl border border-gray-200 bg-gray-50 p-3 outline-none focus:ring-2 focus:ring-emerald-500">
                @foreach(['Cooked Meals', 'Fresh Produce', 'Bakery Items', 'Dairy & Eggs', 'Canned Goods'] as $category)
                    <option value="{{ $category }}" @selected(old('food_category', $donation->food_category) === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('food_category')" />
        </div>

        <div class="space-y-2">
            <label class="mb-2 block text-sm font-semibold">Quantity (Approx kg/servings)</label>
            <div class="flex gap-2">
                <input type="number" step="0.1" name="quantity_kg" value="{{ old('quantity_kg', $donation->quantity_kg) }}" placeholder="e.g. 10" class="w-2/3 rounded-xl border border-gray-200 bg-gray-50 p-3 outline-none focus:ring-2 focus:ring-emerald-500" required>
                <input type="text" name="quantity" value="{{ old('quantity', $donation->quantity) }}" placeholder="e.g. 5 Trays" class="w-full rounded-xl border border-gray-200 bg-gray-50 p-3 outline-none focus:ring-2 focus:ring-emerald-500" required>
            </div>
            <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('quantity_kg')" />
            <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('quantity')" />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <div class="space-y-2">
            <label class="mb-2 block text-sm font-semibold">Expiry/Best Before Time</label>
            <input type="datetime-local" name="expiry_time" value="{{ $expiryValue }}" class="w-full rounded-xl border border-gray-200 bg-gray-50 p-3 outline-none focus:ring-2 focus:ring-emerald-500" required>
            <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('expiry_time')" />
        </div>

        <div class="space-y-2">
            <label class="mb-2 block text-sm font-semibold">Pickup Location</label>
            <div class="relative">
                <i class="fas fa-map-marker-alt absolute left-4 top-4 text-gray-400"></i>
                <input type="text" name="location" value="{{ old('location', $donation->location) }}" placeholder="Street Address, Area" class="w-full rounded-xl border border-gray-200 bg-gray-50 p-3 pl-10 outline-none focus:ring-2 focus:ring-emerald-500" required>
            </div>
            <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('location')" />
        </div>
    </div>

    <div class="space-y-2">
        <label class="mb-2 block text-sm font-semibold">Food Photo (Optional)</label>
        <input type="file" name="image" accept="image/*" class="w-full rounded-xl border border-gray-100 bg-gray-50 p-2 text-sm font-medium">
        @if($isEdit && $donation->image_path)
            <p class="text-xs font-medium text-gray-500">Current image will stay unless you upload a new one.</p>
        @endif
        <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('image')" />
    </div>

    <div class="space-y-2">
        <label class="mb-2 block text-sm font-semibold">Special Instructions</label>
        <textarea name="special_instructions" rows="3" placeholder="Allergens, packaging details, or entry instructions..." class="w-full rounded-xl border border-gray-200 bg-gray-50 p-3 outline-none focus:ring-2 focus:ring-emerald-500">{{ old('special_instructions', $donation->special_instructions) }}</textarea>
        <x-input-error class="mt-2 text-xs font-bold text-red-500" :messages="$errors->get('special_instructions')" />
    </div>

    <button type="submit" class="w-full rounded-xl bg-emerald-600 py-4 text-lg font-bold text-white shadow-lg shadow-emerald-100 transition hover:bg-emerald-700">
        {{ $submitLabel }}
    </button>
</form>
