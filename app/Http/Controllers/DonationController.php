<?php

namespace App\Http\Controllers;

use App\Notifications\DonationClaimed;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category', 'all');
        $query = trim((string) $request->get('q'));

        $donations = Donation::query()
            ->where('status', 'available')
            ->where('expiry_time', '>', now())
            ->with('donor')
            ->when($category && $category !== 'all', fn ($dbQuery) => $dbQuery->where('food_category', $category))
            ->when($query !== '', function ($dbQuery) use ($query) {
                $dbQuery->where(function ($search) use ($query) {
                    $search->where('food_category', 'like', "%{$query}%")
                        ->orWhere('location', 'like', "%{$query}%")
                        ->orWhere('quantity', 'like', "%{$query}%")
                        ->orWhere('special_instructions', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->get();

        return view('donations.index', [
            'donations' => $donations,
            'selectedCategory' => $category,
            'searchQuery' => $query,
        ]);
    }

    public function create()
    {
        if (Auth::user()->role !== 'donor') {
            return redirect()->route('dashboard')->with('error', 'Only donors can post food.');
        }

        return view('donations.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'donor') {
            abort(403);
        }

        $validated = $request->validate([
            'food_category' => 'required|string|max:255',
            'quantity' => 'required|string|max:255',
            'quantity_kg' => 'required|numeric|min:0.1',
            'expiry_time' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'special_instructions' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('donations', 'public');
        }

        $validated['donor_id'] = Auth::id();
        $validated['status'] = 'available';

        Donation::create($validated);

        return redirect()->route('donations.index')->with('success', 'Donation posted successfully!');
    }

    public function update(Request $request, Donation $donation)
    {
        if (Auth::user()->role !== 'receiver') {
            return redirect()->back()->with('error', 'Only NGO/Receiver accounts can claim food.');
        }

        if ($donation->status !== 'available' || $donation->expiry_time->isPast()) {
            return redirect()->back()->with('error', 'Donation no longer available.');
        }

        $donation->update([
            'status' => 'claimed',
            'receiver_id' => Auth::id(),
        ]);

        $receiver = Auth::user();
        $donation->donor->notify(new DonationClaimed($donation, $receiver));

        return redirect()->back()->with('success', 'Donation claimed successfully!');
    }

    public function complete(Donation $donation)
    {
        if ($donation->donor_id !== Auth::id()) {
            abort(403);
        }

        if ($donation->status !== 'claimed') {
            return redirect()->back()->with('error', 'Unable to complete this donation.');
        }

        $donation->update([
            'status' => 'completed',
            'picked_up_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Donation marked as completed/picked up!');
    }
}
