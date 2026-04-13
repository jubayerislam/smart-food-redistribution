@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <div class="mb-12">
        <h2 class="mb-3 text-4xl font-extrabold text-gray-900">Admin Reporting</h2>
        <p class="text-gray-600">Monitor users, listing quality, and donation activity from one dashboard.</p>
    </div>

    <div class="mb-12 grid grid-cols-1 gap-6 md:grid-cols-4">
        @foreach ([
            'Users' => $stats['users'],
            'Active Listings' => $stats['active'],
            'Expired Listings' => $stats['expired'],
            'Completed Pickups' => $stats['completed'],
        ] as $label => $value)
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-lg">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400">{{ $label }}</p>
                <p class="mt-3 text-4xl font-extrabold text-gray-900">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="mb-12 grid grid-cols-1 gap-8 lg:grid-cols-2">
        <div class="rounded-3xl border border-gray-100 bg-white shadow-lg">
            <div class="border-b border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900">Recent Users</h3>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($recentUsers as $user)
                    <div class="flex items-center justify-between p-6">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $user->email }}</p>
                        </div>
                        <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase {{ $user->role === 'admin' ? 'bg-slate-100 text-slate-700' : ($user->role === 'donor' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $user->role }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-gray-100 bg-white shadow-lg">
            <div class="border-b border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900">Account Breakdown</h3>
            </div>
            <div class="space-y-5 p-6 text-sm font-semibold text-gray-700">
                <div class="flex items-center justify-between"><span>Donors</span><span>{{ $stats['donors'] }}</span></div>
                <div class="flex items-center justify-between"><span>Receivers</span><span>{{ $stats['receivers'] }}</span></div>
                <div class="flex items-center justify-between"><span>Admins</span><span>{{ $stats['admins'] }}</span></div>
                <div class="flex items-center justify-between"><span>Claimed Donations</span><span>{{ $stats['claimed'] }}</span></div>
                <div class="flex items-center justify-between"><span>Hidden Listings</span><span>{{ $stats['hidden'] }}</span></div>
                <div class="flex items-center justify-between"><span>Suspended Users</span><span>{{ $stats['suspended'] }}</span></div>
                <div class="flex items-center justify-between"><span>Open Reports</span><span>{{ $stats['open_reports'] }}</span></div>
            </div>
        </div>
    </div>

    <div class="mb-12 overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-lg">
        <div class="border-b border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-900">Report Queue</h3>
            <p class="mt-1 text-sm text-gray-500">Review listing and user reports submitted by the community.</p>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($recentReports as $report)
                <div class="flex flex-col gap-4 p-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-2xl">
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold uppercase text-slate-700">{{ $report->type }}</span>
                            <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase {{ $report->status === 'resolved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $report->status }}</span>
                        </div>
                        <p class="mt-3 text-sm text-gray-700">
                            Reported by <span class="font-bold">{{ $report->reporter?->name ?? 'Unknown user' }}</span>
                            @if($report->donation)
                                about listing <span class="font-bold">{{ $report->donation->food_category }}</span>
                            @endif
                            @if($report->reportedUser)
                                involving <span class="font-bold">{{ $report->reportedUser->organization_name ?? $report->reportedUser->name }}</span>
                            @endif
                        </p>
                        <p class="mt-2 text-sm text-gray-500">{{ $report->reason }}</p>
                        @if($report->admin_notes)
                            <p class="mt-2 text-xs font-medium text-emerald-700">Admin note: {{ $report->admin_notes }}</p>
                        @endif
                    </div>

                    @if($report->status === 'open')
                        <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="w-full max-w-md space-y-3">
                            @csrf
                            <input type="text" name="admin_notes" maxlength="1000" placeholder="Optional resolution note" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                            <button type="submit" class="rounded-lg bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">
                                Mark Resolved
                            </button>
                        </form>
                    @else
                        <p class="text-xs font-medium text-gray-400">Resolved {{ $report->resolved_at?->diffForHumans() }}</p>
                    @endif
                </div>
            @empty
                <div class="p-8 text-sm font-medium text-gray-400">No reports submitted yet.</div>
            @endforelse
        </div>
    </div>

    <div class="mb-12 overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-lg">
        <div class="border-b border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-900">Moderate Users</h3>
            <p class="mt-1 text-sm text-gray-500">Suspend unsafe donor or receiver accounts and restore them after review.</p>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($recentUsers as $user)
                <div class="flex flex-col gap-4 p-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <div class="flex items-center gap-3">
                            <p class="font-semibold text-gray-900">{{ $user->name }}</p>
                            <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase {{ $user->role === 'admin' ? 'bg-slate-100 text-slate-700' : ($user->role === 'donor' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $user->role }}
                            </span>
                            @if($user->isSuspended())
                                <span class="rounded-full bg-rose-100 px-3 py-1 text-[10px] font-bold uppercase text-rose-700">Suspended</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-500">{{ $user->email }}</p>
                        @if($user->isSuspended() && $user->suspension_reason)
                            <p class="mt-2 text-xs font-medium text-rose-600">Reason: {{ $user->suspension_reason }}</p>
                        @endif
                    </div>

                    @if($user->role !== 'admin')
                        <div class="w-full max-w-xl">
                            @if($user->isSuspended())
                                <form action="{{ route('admin.users.restore', $user) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="rounded-lg bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700 transition hover:bg-emerald-100">
                                        Restore Access
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.suspend', $user) }}" method="POST" class="flex flex-col gap-3 sm:flex-row">
                                    @csrf
                                    <input type="text" name="reason" required maxlength="1000" placeholder="Reason for suspension" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                    <button type="submit" class="rounded-lg bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700 transition hover:bg-rose-100">
                                        Suspend User
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-lg">
        <div class="border-b border-gray-100 p-6">
            <h3 class="text-xl font-bold text-gray-900">Moderate Donations</h3>
            <p class="mt-1 text-sm text-gray-500">Hide suspicious listings from the marketplace or restore them after review.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Donation</th>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Donor</th>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Receiver</th>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="p-6 text-xs font-bold uppercase tracking-wider text-gray-400">Moderation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentDonations as $donation)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-6">
                                <p class="font-bold text-gray-900">{{ $donation->food_category }}</p>
                                <p class="text-xs text-gray-400">{{ $donation->quantity_kg }} kg</p>
                            </td>
                            <td class="p-6 text-sm font-semibold text-gray-700">{{ $donation->donor?->organization_name ?? $donation->donor?->name ?? 'Unknown' }}</td>
                            <td class="p-6 text-sm font-semibold text-gray-700">{{ $donation->receiver?->organization_name ?? $donation->receiver?->name ?? 'Unclaimed' }}</td>
                            <td class="p-6">
                                <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase {{ $donation->display_status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($donation->display_status === 'expired' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $donation->display_status }}
                                </span>
                                @if($donation->is_hidden)
                                    <span class="ml-2 rounded-full bg-rose-100 px-3 py-1 text-[10px] font-bold uppercase text-rose-700">
                                        hidden
                                    </span>
                                @endif
                            </td>
                            <td class="p-6">
                                <div class="flex min-w-64 flex-col gap-3">
                                    @if($donation->moderation_reason)
                                        <p class="text-xs text-gray-500">{{ $donation->moderation_reason }}</p>
                                    @endif

                                    @if($donation->is_hidden)
                                        <form action="{{ route('admin.donations.restore', $donation) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded-lg bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                                                Restore Listing
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.donations.hide', $donation) }}" method="POST" class="flex flex-col gap-3">
                                            @csrf
                                            <input type="text" name="reason" required maxlength="1000" placeholder="Reason for hiding this listing" class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                                            <button type="submit" class="rounded-lg bg-rose-50 px-4 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                                Hide Listing
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
