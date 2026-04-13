<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->role === 'admin', 403);

        $allDonations = Donation::with(['donor', 'receiver'])->latest()->get();

        return view('admin.index', [
            'stats' => [
                'users' => User::count(),
                'donors' => User::where('role', 'donor')->count(),
                'receivers' => User::where('role', 'receiver')->count(),
                'admins' => User::where('role', 'admin')->count(),
                'suspended' => User::whereNotNull('suspended_at')->count(),
                'active' => $allDonations->filter(fn (Donation $donation) => $donation->status === 'available' && ! $donation->isExpired())->count(),
                'expired' => $allDonations->filter(fn (Donation $donation) => $donation->isExpired())->count(),
                'claimed' => $allDonations->where('status', 'claimed')->count(),
                'completed' => $allDonations->where('status', 'completed')->count(),
                'hidden' => $allDonations->where('is_hidden', true)->count(),
                'open_reports' => Report::where('status', 'open')->count(),
            ],
            'recentUsers' => User::latest()->take(8)->get(),
            'recentDonations' => $allDonations->take(10),
            'recentReports' => Report::with(['reporter', 'donation', 'reportedUser'])->latest()->take(10)->get(),
        ]);
    }

    public function hideDonation(Request $request, Donation $donation): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $donation->update([
            'is_hidden' => true,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
            'moderation_reason' => $validated['reason'],
        ]);

        return back()->with('success', 'Donation hidden from the marketplace.');
    }

    public function restoreDonation(Request $request, Donation $donation): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        $donation->update([
            'is_hidden' => false,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
            'moderation_reason' => null,
        ]);

        return back()->with('success', 'Donation restored to the marketplace.');
    }

    public function suspendUser(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        abort_if($user->role === 'admin', 422, 'Admin accounts cannot be suspended.');

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $user->update([
            'suspended_at' => now(),
            'suspension_reason' => $validated['reason'],
        ]);

        Donation::where('donor_id', $user->id)
            ->where('status', 'available')
            ->update([
                'is_hidden' => true,
                'moderated_by' => $request->user()->id,
                'moderated_at' => now(),
                'moderation_reason' => 'Hidden automatically because the donor account was suspended.',
            ]);

        return back()->with('success', 'User suspended and active listings hidden.');
    }

    public function restoreUser(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        $user->update([
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        return back()->with('success', 'User access restored.');
    }

    public function resolveReport(Request $request, Report $report): RedirectResponse
    {
        abort_unless($request->user()->role === 'admin', 403);

        $report->update([
            'status' => 'resolved',
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
            'admin_notes' => $request->input('admin_notes'),
        ]);

        return back()->with('success', 'Report marked as resolved.');
    }
}
