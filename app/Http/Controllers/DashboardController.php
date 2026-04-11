<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if ($user->role === 'donor') {
            $myDonations = Donation::where('donor_id', $user->id)
                ->with('receiver')
                ->latest()
                ->get();

            return view('dashboard', [
                'myDonations' => $myDonations,
                'totalSavedKg' => round($myDonations->sum('quantity_kg'), 1),
                'activeListings' => $myDonations->where('status', 'available')->count(),
                'completedPickups' => $myDonations->where('status', 'completed')->count(),
                'role' => 'donor',
            ]);
        }

        $myClaims = Donation::where('receiver_id', $user->id)
            ->with('donor')
            ->latest()
            ->get();

        return view('dashboard', [
            'myClaims' => $myClaims,
            'claimedWeight' => round($myClaims->sum('quantity_kg'), 1),
            'completedClaims' => $myClaims->where('status', 'completed')->count(),
            'role' => 'receiver',
        ]);
    }
}
