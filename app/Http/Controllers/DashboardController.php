<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $recentNotifications = $user->notifications()->latest()->limit(5)->get();
        $unreadNotificationsCount = $user->unreadNotifications()->count();

        if ($user->role === 'admin') {
            return redirect()->route('admin.index');
        }

        if ($user->role === 'donor') {
            $myDonations = Donation::where('donor_id', $user->id)
                ->with('receiver')
                ->latest()
                ->get();

            $expiredListings = $myDonations->filter(fn (Donation $donation) => $donation->isExpired())->count();
            $activeListings = $myDonations->filter(fn (Donation $donation) => $donation->status === 'available' && ! $donation->isExpired())->count();

            return view('dashboard', [
                'myDonations' => $myDonations,
                'totalSavedKg' => round($myDonations->sum('quantity_kg'), 1),
                'activeListings' => $activeListings,
                'expiredListings' => $expiredListings,
                'completedPickups' => $myDonations->where('status', 'completed')->count(),
                'recentNotifications' => $recentNotifications,
                'unreadNotificationsCount' => $unreadNotificationsCount,
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
            'recentNotifications' => $recentNotifications,
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'role' => 'receiver',
        ]);
    }
}
