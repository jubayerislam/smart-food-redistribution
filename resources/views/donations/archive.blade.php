@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="mb-3 text-4xl font-bold text-gray-900">Donation Archive</h2>
            <p class="text-gray-600">Review expired listings and completed pickups from your donor history.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="text-sm font-bold text-emerald-600 hover:underline">Back to dashboard</a>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-lg">
        <div class="border-b border-gray-100 bg-gray-50/50 p-8">
            <h3 class="text-xl font-bold text-gray-900">Archived Donations</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Details</th>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Weight</th>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Receiver</th>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Archive Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($archivedDonations as $donation)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-6">
                                <p class="font-bold text-gray-900">{{ $donation->food_category }}</p>
                                <p class="text-xs font-medium text-gray-400">{{ $donation->location }} | {{ $donation->created_at->format('M d, H:i') }}</p>
                            </td>
                            <td class="p-6">
                                <p class="font-bold text-gray-700">{{ $donation->quantity_kg }} kg</p>
                                <p class="text-xs text-gray-400">{{ $donation->quantity }}</p>
                            </td>
                            <td class="p-6">
                                @if($donation->receiver)
                                    <span class="font-bold text-gray-800 text-sm">{{ $donation->receiver->organization_name ?? $donation->receiver->name }}</span>
                                @else
                                    <span class="text-xs italic text-gray-400">No receiver assigned</span>
                                @endif
                            </td>
                            <td class="p-6">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase {{ $donation->display_status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $donation->display_status }}
                                    </span>
                                    <form action="{{ route('donations.relist', $donation) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-sky-50 px-4 py-2 text-xs font-bold text-sky-700 transition hover:bg-sky-100">
                                            Re-list
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center font-bold italic text-gray-400">No archived donations yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
