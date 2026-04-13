<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
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
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/donations/{donation}/hide', [AdminController::class, 'hideDonation'])->name('admin.donations.hide');
    Route::post('/admin/donations/{donation}/restore', [AdminController::class, 'restoreDonation'])->name('admin.donations.restore');
    Route::post('/admin/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('admin.users.suspend');
    Route::post('/admin/users/{user}/restore', [AdminController::class, 'restoreUser'])->name('admin.users.restore');
    Route::post('/admin/reports/{report}/resolve', [AdminController::class, 'resolveReport'])->name('admin.reports.resolve');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

Route::middleware('auth')->group(function () {
    Route::get('/donate', [DonationController::class, 'create'])->name('donations.create');
    Route::post('/donate', [DonationController::class, 'store'])->name('donations.store');
    Route::get('/donations/{donation}/edit', [DonationController::class, 'edit'])->name('donations.edit');
    Route::patch('/donations/{donation}', [DonationController::class, 'saveChanges'])->name('donations.update');
    Route::get('/donations/archive', [DonationController::class, 'archive'])->name('donations.archive');
    Route::post('/donations/{donation}/relist', [DonationController::class, 'relist'])->name('donations.relist');
    Route::get('/marketplace', [DonationController::class, 'index'])->name('donations.index');
    Route::post('/marketplace/{donation}/claim', [DonationController::class, 'update'])->name('donations.claim');
    Route::post('/marketplace/{donation}/complete', [DonationController::class, 'complete'])->name('donations.complete');
    Route::post('/reports/donations/{donation}', [ReportController::class, 'storeDonation'])->name('reports.donations.store');
    Route::post('/reports/users/{user}', [ReportController::class, 'storeUser'])->name('reports.users.store');
    Route::delete('/donations/{donation}', [DonationController::class, 'destroy'])->name('donations.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
