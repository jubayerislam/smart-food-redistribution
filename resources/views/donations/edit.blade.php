@extends('layouts.app')

@section('content')
<section class="page-shell pt-24 pb-12">
    <div class="mb-10 text-center">
        <h2 class="mb-4 text-4xl font-bold">Edit Donation</h2>
        <p class="text-gray-600">Update the food details so receivers always see the latest pickup information.</p>
    </div>

    <div class="mx-auto max-w-4xl rounded-3xl border border-gray-100 bg-white p-8 shadow-xl">
        @include('donations._form', [
            'action' => route('donations.update', $donation),
            'method' => 'PATCH',
            'submitLabel' => 'Save Donation Changes',
            'donation' => $donation,
        ])
    </div>
</section>
@endsection
