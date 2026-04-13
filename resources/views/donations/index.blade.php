@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
        <div>
            <h2 class="text-4xl font-bold mb-2">Available Food Near You</h2>
            <p class="text-gray-600">Claim donations for your organization.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <form action="{{ route('donations.index') }}" method="GET" class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-emerald-500 transition"></i>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search location..." 
                    class="w-full pl-11 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-white text-sm">
            </form>
            <div class="flex gap-2">
                <a href="{{ route('donations.index', ['category' => 'all']) }}" class="bg-gray-200 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-300 transition {{ request('category', 'all') === 'all' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-700' }}">All</a>
                <a href="{{ route('donations.index', ['category' => 'Cooked Meals']) }}" class="bg-gray-200 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-300 transition {{ request('category') === 'Cooked Meals' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-700' }}">Cooked</a>
                <a href="{{ route('donations.index', ['category' => 'Fresh Produce']) }}" class="bg-gray-200 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-300 transition {{ request('category') === 'Fresh Produce' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-700' }}">Fresh</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($donations as $donation)
            <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100 card-hover flex flex-col">
                <div class="h-48 bg-emerald-100 relative group">
                    <span class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-[10px] font-bold shadow-lg">
                        Expires: {{ $donation->expiry_time->diffForHumans() }}
                    </span>
                    @if($donation->image_path)
                        <img src="{{ Storage::url($donation->image_path) }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                    @else
                        <div class="flex items-center justify-center h-full">
                            <i class="fas fa-{{ $donation->food_category === 'Cooked Meals' ? 'utensils' : 'shopping-basket' }} text-6xl text-emerald-300"></i>
                        </div>
                    @endif
                </div>
                <div class="p-6 flex flex-1 flex-col">
                    <h3 class="font-bold text-xl mb-1 text-gray-900">{{ $donation->food_category }}</h3>
                    <p class="text-gray-500 text-sm mb-4"><i class="fas fa-store mr-1 text-emerald-500"></i> {{ $donation->donor->organization_name ?? $donation->donor->name }}</p>
                    <p class="text-xs text-gray-400 font-bold mb-4 uppercase"><i class="fas fa-location-dot mr-1"></i> {{ $donation->location }}</p>
                    
                    <div class="flex justify-between items-center py-4 border-t border-gray-100 mt-auto">
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Quantity</p>
                            <p class="font-bold text-gray-900">{{ $donation->quantity_kg }} kg</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Details</p>
                            <p class="font-bold text-emerald-600">{{ $donation->quantity }}</p>
                        </div>
                    </div>
                    
                    @if(auth()->user()->role === 'receiver')
                        <form action="{{ route('donations.claim', $donation) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full mt-4 bg-emerald-600 text-white py-3 rounded-xl font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-50">
                                Claim Now
                            </button>
                        </form>
                    @elseif(auth()->id() === $donation->donor_id)
                        <div class="mt-4 w-full rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-center text-sm font-semibold text-amber-700">
                            This is your listing
                        </div>
                    @else
                        <div class="mt-4 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-center text-sm font-semibold text-gray-500">
                            Receiver accounts can claim this donation
                        </div>
                    @endif

                    @if(auth()->id() !== $donation->donor_id && auth()->user()->role !== 'admin')
                        <div class="mt-4 space-y-3 border-t border-gray-100 pt-4">
                            <form action="{{ route('reports.donations.store', $donation) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="text" name="reason" required maxlength="1000" placeholder="Report this listing" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                <button type="submit" class="w-full rounded-xl bg-rose-50 px-4 py-2 text-sm font-bold text-rose-700 transition hover:bg-rose-100">
                                    Report Listing
                                </button>
                            </form>

                            <form action="{{ route('reports.users.store', $donation->donor) }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="text" name="reason" required maxlength="1000" placeholder="Report this donor" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                <button type="submit" class="w-full rounded-xl bg-gray-100 px-4 py-2 text-sm font-bold text-gray-700 transition hover:bg-gray-200">
                                    Report Donor
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-box-open text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">No Food Found</h3>
                <p class="text-gray-500 mt-2">There are no donations available in this category right now.</p>
                <a href="{{ route('home') }}" class="inline-block mt-8 text-emerald-600 font-bold hover:underline">Back to Home</a>
            </div>
        @endforelse
    </div>
</section>
@endsection
