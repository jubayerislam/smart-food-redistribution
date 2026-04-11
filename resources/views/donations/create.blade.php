@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <div class="mb-10 text-center">
        <h2 class="text-4xl font-bold mb-4">Post a Donation</h2>
        <p class="text-gray-600">List your surplus food. We'll match you with a nearby receiver instantly.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100 max-w-4xl mx-auto">
        <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold mb-2">Food Category</label>
                    <select name="food_category" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50 appearance-none cursor-pointer">
                        <option value="Cooked Meals">Cooked Meals</option>
                        <option value="Fresh Produce">Fresh Produce</option>
                        <option value="Bakery Items">Bakery Items</option>
                        <option value="Dairy & Eggs">Dairy & Eggs</option>
                        <option value="Canned Goods">Canned Goods</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold mb-2">Quantity (Approx kg/servings)</label>
                    <div class="flex gap-2">
                        <input type="number" step="0.1" name="quantity_kg" placeholder="e.g. 10" class="w-2/3 p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50" required>
                        <input type="text" name="quantity" placeholder="e.g. 5 Trays" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50" required>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-semibold mb-2">Expiry/Best Before Time</label>
                    <input type="datetime-local" name="expiry_time" class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50" required>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold mb-2">Pickup Location</label>
                    <div class="relative">
                        <i class="fas fa-map-marker-alt absolute left-4 top-4 text-gray-400"></i>
                        <input type="text" name="location" placeholder="Street Address, Area" class="w-full p-3 pl-10 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50" required>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold mb-2">Food Photo (Optional)</label>
                <input type="file" name="image" accept="image/*" class="w-full p-2 border border-gray-100 rounded-xl bg-gray-50 text-sm font-medium">
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold mb-2">Special Instructions</label>
                <textarea name="special_instructions" rows="3" placeholder="Allergens, packaging details, or entry instructions..." class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-gray-50"></textarea>
            </div>

            <button type="submit" class="w-full bg-emerald-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-emerald-700 transition shadow-lg shadow-emerald-100">
                Submit Donation for Matching
            </button>
        </form>
    </div>
</section>
@endsection
