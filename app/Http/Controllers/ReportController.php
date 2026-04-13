<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function storeDonation(Request $request, Donation $donation): RedirectResponse
    {
        abort_if($request->user()->id === $donation->donor_id, 422, 'You cannot report your own listing.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        Report::firstOrCreate([
            'type' => 'donation',
            'status' => 'open',
            'reporter_id' => $request->user()->id,
            'donation_id' => $donation->id,
        ], [
            'reported_user_id' => $donation->donor_id,
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Listing reported for admin review.');
    }

    public function storeUser(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->id === $user->id, 422, 'You cannot report your own account.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        Report::firstOrCreate([
            'type' => 'user',
            'status' => 'open',
            'reporter_id' => $request->user()->id,
            'reported_user_id' => $user->id,
        ], [
            'reason' => $validated['reason'],
        ]);

        return back()->with('success', 'User reported for admin review.');
    }
}
