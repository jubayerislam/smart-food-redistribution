@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="mb-3 text-4xl font-bold text-gray-900">Notifications</h2>
            <p class="text-gray-600">Track claim, pickup, and account activity in one place.</p>
        </div>
        <form action="{{ route('notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit" class="secondary-btn">Mark All Read</button>
        </form>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach (['all' => 'All', 'unread' => 'Unread', 'read' => 'Read'] as $value => $label)
            <a href="{{ route('notifications.index', ['filter' => $value]) }}"
               class="rounded-full px-4 py-2 text-sm font-bold transition {{ $filter === $value ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:text-emerald-600' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-lg">
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <div class="flex flex-col gap-4 p-6 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $notification->data['message'] ?? 'New notification' }}</p>
                        <p class="mt-1 text-xs font-medium text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        @if(! empty($notification->data['action_url']))
                            <a href="{{ $notification->data['action_url'] }}" class="text-sm font-bold text-emerald-600 hover:underline">
                                Open
                            </a>
                        @endif
                        @if(is_null($notification->read_at))
                            <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                @csrf
                                <button type="submit" class="rounded-lg bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                                    Mark as Read
                                </button>
                            </form>
                        @else
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-[10px] font-bold uppercase text-gray-500">
                                Read
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-10 text-center text-sm font-medium text-gray-400">No notifications found for this filter.</div>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</section>
@endsection
