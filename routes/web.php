<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ProfileController;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $totalKg = (float) Donation::sum('quantity_kg');

    $stats = [
        'total_kg' => $totalKg,
        'estimated_meals' => (int) round($totalKg * 2),
        'co2_offset_kg' => round($totalKg * 3.5, 1),
        'donor_count' => User::where('role', 'donor')->count(),
        'receiver_count' => User::where('role', 'receiver')->count(),
        'active_listings' => Donation::where('status', 'available')
            ->where('expiry_time', '>', now())
            ->count(),
    ];

    $recentDonations = Donation::with('donor')
        ->where('expiry_time', '>', now()->subDays(7))
        ->latest()
        ->limit(3)
        ->get();

    return view('home', compact('stats', 'recentDonations'));
})->name('home');

Route::get('/impact', function () {
    $totalKg = (float) Donation::sum('quantity_kg');
    $donations = Donation::with('donor')->orderBy('created_at', 'asc')->get();

    $stats = [
        'total_kg' => $totalKg,
        'estimated_meals' => (int) round($totalKg * 2),
        'co2_offset_kg' => round($totalKg * 3.5, 1),
        'donor_count' => User::where('role', 'donor')->count(),
        'receiver_count' => User::where('role', 'receiver')->count(),
        'total_claims' => Donation::whereIn('status', ['claimed', 'completed'])->count(),
        'completed_pickups' => Donation::where('status', 'completed')->count(),
        'active_listings' => Donation::where('status', 'available')
            ->where('expiry_time', '>', now())
            ->count(),
    ];

    $monthlyImpact = $donations
        ->groupBy(fn (Donation $donation) => $donation->created_at->format('M'))
        ->map(fn ($items, $month) => [
            'month' => $month,
            'kg' => round($items->sum('quantity_kg'), 1),
        ])
        ->values()
        ->take(6);

    $topContributors = $donations
        ->groupBy('donor_id')
        ->map(function ($items) {
            $donor = $items->first()->donor;

            return [
                'name' => $donor?->organization_name ?: $donor?->name ?: 'Unknown donor',
                'type' => $donor?->organization_name ? 'Organization' : 'Individual',
                'total_kg' => round($items->sum('quantity_kg'), 1),
                'donations_count' => $items->count(),
            ];
        })
        ->sortByDesc('total_kg')
        ->take(5)
        ->values();

    return view('impact', compact('stats', 'monthlyImpact', 'topContributors'));
})->name('impact');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/donate', [DonationController::class, 'create'])->name('donations.create');
    Route::post('/donate', [DonationController::class, 'store'])->name('donations.store');
    Route::get('/marketplace', [DonationController::class, 'index'])->name('donations.index');
    Route::post('/marketplace/{donation}/claim', [DonationController::class, 'update'])->name('donations.claim');
    Route::post('/marketplace/{donation}/complete', [DonationController::class, 'complete'])->name('donations.complete');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
