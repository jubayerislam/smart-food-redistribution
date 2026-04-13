@extends('layouts.app')

@section('content')
<!-- Dashboard Content -->
<section id="dashboard" class="pt-24 pb-12">
    <div class="page-shell">
        <!-- Header -->
        <div class="mb-12 flex flex-col md:flex-row justify-between items-end gap-6">
            <div>
                <h2 class="text-4xl font-extrabold mb-2 text-gray-900">{{ ucfirst($role) }} Dashboard</h2>
                <p class="text-gray-600">Welcome back, <span class="font-bold text-emerald-600">{{ Auth::user()->name }}</span>. Here is your redistribution overview.</p>
            </div>
            @if($role === 'donor')
                <div class="flex gap-3">
                    <a href="{{ route('donations.archive') }}" class="secondary-btn">
                        View Archive
                    </a>
                    <a href="{{ route('donations.create') }}" class="primary-btn">
                        Post a Donation
                    </a>
                </div>
            @endif
        </div>

        <!-- Quick Stats (Donor/Receiver specific) -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12 text-center">
            @if($role === 'donor')
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Active Listings</p>
                    <p class="text-5xl font-extrabold text-gray-900 mb-2">{{ $activeListings }}</p>
                    <p class="text-sm font-bold text-emerald-600 uppercase">Available for claim</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Expired Listings</p>
                    <p class="text-5xl font-extrabold text-gray-900 mb-2">{{ $expiredListings }}</p>
                    <p class="text-sm font-bold text-amber-600 uppercase">Needs archive review</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Completed Rescues</p>
                    <p class="text-5xl font-extrabold text-gray-900 mb-2">{{ $completedPickups }}</p>
                    <p class="text-sm font-bold text-emerald-600 uppercase">Successfully Handed Over</p>
                </div>
                <div class="bg-emerald-600 p-8 rounded-3xl shadow-lg text-white">
                    <p class="text-xs font-bold text-emerald-100 opacity-80 uppercase tracking-widest mb-2">CO2 Offset (kg)</p>
                    <p class="text-5xl font-extrabold mb-2">{{ number_format($totalSavedKg * 2.5, 1) }}</p>
                    <p class="text-sm font-bold uppercase tracking-widest">Environmental Impact</p>
                </div>
            @else
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Total Rescues</p>
                    <p class="text-5xl font-extrabold text-gray-900 mb-2">{{ $completedClaims }}</p>
                    <p class="text-sm font-bold text-emerald-600 uppercase">Donations Picked Up</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-lg border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Claimed Weight</p>
                    <p class="text-5xl font-extrabold text-gray-900 mb-2">{{ number_format($claimedWeight, 1) }} kg</p>
                    <p class="text-sm font-bold text-emerald-600 uppercase">Food Redistributed</p>
                </div>
                <div class="bg-emerald-600 p-8 rounded-3xl shadow-lg text-white">
                    <p class="text-xs font-bold text-emerald-100 opacity-80 uppercase tracking-widest mb-2">Pending Claim</p>
                    <p class="text-5xl font-extrabold mb-2">{{ $myClaims->where('status', 'claimed')->count() }}</p>
                    <a href="{{ route('donations.index') }}" class="text-xs font-bold underline hover:text-emerald-100">Browse Marketplace</a>
                </div>
            @endif
        </div>

        <div class="mb-12 overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-lg">
            <div class="flex flex-col gap-3 border-b border-gray-100 bg-gray-50/50 p-8 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Notifications</h3>
                    <p class="text-sm text-gray-500">Recent updates from your redistribution activity.</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex w-fit items-center rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-widest text-emerald-700">
                        {{ $unreadNotificationsCount }} new
                    </span>
                    <a href="{{ route('notifications.index') }}" class="text-sm font-bold text-emerald-600 hover:underline">
                        View all
                    </a>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentNotifications as $notification)
                    <div class="flex flex-col gap-3 p-6 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $notification->data['message'] ?? 'New activity update' }}</p>
                            <p class="text-xs font-medium text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            @if(! empty($notification->data['action_url']))
                                <a href="{{ $notification->data['action_url'] }}" class="text-sm font-bold text-emerald-600 hover:underline">
                                    View details
                                </a>
                            @endif
                            @if(is_null($notification->read_at))
                                <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                                        Mark as Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-sm font-medium text-gray-400">
                        No notifications yet. Claims and pickups will appear here.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Activity Table (Based on Mockup Table Style) -->
        <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-xl font-bold text-gray-900">
                    {{ $role === 'donor' ? 'My Donation Logs' : 'My Claim History' }}
                </h3>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recent Activity</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="p-6 text-xs font-bold text-gray-400 uppercase tracking-wider">Details</th>
                            <th class="p-6 text-xs font-bold text-gray-400 uppercase tracking-wider">Weight</th>
                            <th class="p-6 text-xs font-bold text-gray-400 uppercase tracking-wider">Partner</th>
                            <th class="p-6 text-xs font-bold text-gray-400 uppercase tracking-wider">Badge / Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if($role === 'donor')
                            @forelse($myDonations as $donation)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-6">
                                        <p class="font-bold text-gray-900">{{ $donation->food_category }}</p>
                                        <p class="text-xs text-gray-400 font-medium">{{ $donation->created_at->format('M d, H:i') }}</p>
                                    </td>
                                    <td class="p-6">
                                        <p class="font-bold text-gray-700">{{ $donation->quantity_kg }} kg</p>
                                        <p class="text-xs text-gray-400">{{ $donation->quantity }}</p>
                                    </td>
                                    <td class="p-6">
                                        @if($donation->receiver)
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">
                                                    {{ substr($donation->receiver->organization_name ?? $donation->receiver->name, 0, 1) }}
                                                </div>
                                                <span class="font-bold text-gray-800 text-sm">{{ $donation->receiver->organization_name ?? $donation->receiver->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Awaiting NGO...</span>
                                        @endif
                                    </td>
                                    <td class="p-6">
                                        @if($donation->status === 'claimed')
                                            <form action="{{ route('donations.complete', $donation) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-emerald-700 transition shadow-lg shadow-emerald-100">
                                                    Mark Picked Up
                                                </button>
                                            </form>
                                        @elseif($donation->display_status === 'expired')
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-700">
                                                expired
                                            </span>
                                        @elseif($donation->status === 'available')
                                            <div class="flex flex-wrap gap-2">
                                                <a href="{{ route('donations.edit', $donation) }}" class="rounded-lg bg-sky-50 px-4 py-2 text-xs font-bold text-sky-600 transition hover:bg-sky-100">
                                                    Edit
                                                </a>
                                                <form action="{{ route('donations.destroy', $donation) }}" method="POST" onsubmit="return confirm('Remove this donation listing?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="rounded-lg bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-100">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $donation->display_status === 'available' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                {{ $donation->display_status }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-10 text-center text-gray-400 italic font-bold">No donations posted yet.</td></tr>
                            @endforelse
                        @else
                            @forelse($myClaims as $claim)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="p-6">
                                        <p class="font-bold text-gray-900">{{ $claim->food_category }}</p>
                                        <p class="text-xs text-gray-400 font-medium">{{ $claim->created_at->format('M d, H:i') }}</p>
                                    </td>
                                    <td class="p-6 font-bold text-gray-700 text-sm">
                                        {{ $claim->quantity_kg }} kg
                                    </td>
                                    <td class="p-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 text-xs font-bold">
                                                <i class="fas fa-store"></i>
                                            </div>
                                            <span class="font-bold text-gray-800 text-sm">{{ $claim->donor->organization_name ?? $claim->donor->name }}</span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase {{ $claim->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $claim->status === 'completed' ? 'Picked Up' : 'Claimed' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-10 text-center text-gray-400 italic font-bold">No active claims found.</td></tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
