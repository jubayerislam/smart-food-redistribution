<?php

namespace App\Http\Controllers;

use App\Notifications\DonationCompleted;
use App\Notifications\DonationClaimed;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    protected function donationRules(): array
    {
        return [
            'food_category' => 'required|string|max:255',
            'quantity' => 'required|string|max:255',
            'quantity_kg' => 'required|numeric|min:0.1',
            'expiry_time' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'special_instructions' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function index(Request $request)
    {
        $category = $request->get('category', 'all');
        $query = trim((string) $request->get('q'));

        $donations = Donation::query()
            ->where('status', 'available')
            ->where('is_hidden', false)
            ->where('expiry_time', '>', now())
            ->with('donor')
            ->whereHas('donor', fn ($donorQuery) => $donorQuery->whereNull('suspended_at'))
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
        $this->authorize('create', Donation::class);

        return view('donations.create', [
            'donation' => new Donation(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Donation::class);

        $validated = $request->validate($this->donationRules());

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('donations', 'public');
        }

        $validated['donor_id'] = Auth::id();
        $validated['status'] = 'available';

        Donation::create($validated);

        return redirect()->route('donations.index')->with('success', 'Donation posted successfully!');
    }

    public function edit(Donation $donation)
    {
        $this->authorize('update', $donation);

        return view('donations.edit', [
            'donation' => $donation,
        ]);
    }

    public function saveChanges(Request $request, Donation $donation)
    {
        $this->authorize('update', $donation);

        $validated = $request->validate($this->donationRules());

        if ($request->hasFile('image')) {
            $oldImagePath = $donation->image_path;
            $validated['image_path'] = $request->file('image')->store('donations', 'public');

            if ($oldImagePath !== $validated['image_path']) {
                $this->deleteDonationImage($oldImagePath);
            }
        } else {
            $validated['image_path'] = $donation->image_path;
        }

        $donation->update($validated);

        return redirect()->route('dashboard')->with('success', 'Donation updated successfully.');
    }

    public function update(Request $request, Donation $donation)
    {
        $this->authorize('claim', $donation);

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
        $this->authorize('complete', $donation);

        $donation->update([
            'status' => 'completed',
            'picked_up_at' => now(),
        ]);

        if ($donation->receiver) {
            $donation->receiver->notify(new DonationCompleted($donation));
        }

        return redirect()->back()->with('success', 'Donation marked as completed/picked up!');
    }

    public function archive()
    {
        $this->authorize('viewArchive', Donation::class);

        $archivedDonations = Donation::where('donor_id', Auth::id())
            ->with('receiver')
            ->latest()
            ->get()
            ->filter(fn (Donation $donation) => in_array($donation->display_status, ['expired', 'completed'], true))
            ->values();

        return view('donations.archive', [
            'archivedDonations' => $archivedDonations,
        ]);
    }

    public function relist(Donation $donation)
    {
        $this->authorize('relist', $donation);

        $newDonation = $donation->replicate([
            'status',
            'receiver_id',
            'picked_up_at',
            'created_at',
            'updated_at',
        ]);

        $newDonation->status = 'available';
        $newDonation->is_hidden = false;
        $newDonation->receiver_id = null;
        $newDonation->moderated_by = null;
        $newDonation->moderated_at = null;
        $newDonation->moderation_reason = null;
        $newDonation->picked_up_at = null;
        $newDonation->expiry_time = now()->addDay();
        $newDonation->image_path = $this->duplicateDonationImage($donation->image_path);
        $newDonation->save();

        return redirect()->route('donations.edit', $newDonation)->with('success', 'Donation duplicated as a new listing. Update it and publish your latest pickup window.');
    }

    public function destroy(Donation $donation)
    {
        $this->authorize('delete', $donation);

        $this->deleteDonationImage($donation->image_path);

        $donation->delete();

        return redirect()->back()->with('success', 'Donation listing removed successfully.');
    }

    protected function deleteDonationImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function duplicateDonationImage(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return $path;
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $copyPath = 'donations/'.Str::uuid().($extension ? '.'.$extension : '');

        Storage::disk('public')->copy($path, $copyPath);

        return $copyPath;
    }
}
