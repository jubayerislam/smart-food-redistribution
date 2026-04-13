@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <div class="mb-10 text-center">
        <h2 class="text-4xl font-bold mb-4">Post a Donation</h2>
        <p class="text-gray-600">List your surplus food. We'll match you with a nearby receiver instantly.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl p-8 border border-gray-100 max-w-4xl mx-auto">
        @include('donations._form', [
            'action' => route('donations.store'),
            'method' => 'POST',
            'submitLabel' => 'Submit Donation for Matching',
            'donation' => $donation,
        ])
    </div>
</section>
@endsection
