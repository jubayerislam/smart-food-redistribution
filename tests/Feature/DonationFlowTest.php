<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DonationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_receiver_can_claim_an_available_donation(): void
    {
        $donor = User::factory()->create([
            'role' => 'donor',
            'organization_name' => 'Green Valley Hotel',
        ]);

        $receiver = User::factory()->create([
            'role' => 'receiver',
            'organization_name' => 'City Hope Foundation',
        ]);

        $donation = Donation::create([
            'food_category' => 'Cooked Meals',
            'quantity' => '30 Servings',
            'quantity_kg' => 18.5,
            'expiry_time' => now()->addDay(),
            'location' => 'Dhanmondi',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        $response = $this
            ->actingAs($receiver)
            ->post(route('donations.claim', $donation));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $donation->refresh();

        $this->assertSame('claimed', $donation->status);
        $this->assertSame($receiver->id, $donation->receiver_id);
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_donor_cannot_claim_a_donation(): void
    {
        $owner = User::factory()->create(['role' => 'donor']);
        $otherDonor = User::factory()->create(['role' => 'donor']);

        $donation = Donation::create([
            'food_category' => 'Fresh Produce',
            'quantity' => '12 Boxes',
            'quantity_kg' => 24,
            'expiry_time' => now()->addDay(),
            'location' => 'Banani',
            'status' => 'available',
            'donor_id' => $owner->id,
        ]);

        $response = $this
            ->actingAs($otherDonor)
            ->post(route('donations.claim', $donation));

        $response->assertForbidden();

        $this->assertSame('available', $donation->fresh()->status);
    }

    public function test_donor_can_remove_their_available_listing(): void
    {
        $donor = User::factory()->create(['role' => 'donor']);

        $donation = Donation::create([
            'food_category' => 'Bakery Items',
            'quantity' => '20 Packs',
            'quantity_kg' => 10,
            'expiry_time' => now()->addDay(),
            'location' => 'Gulshan',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        $response = $this
            ->actingAs($donor)
            ->delete(route('donations.destroy', $donation));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('donations', ['id' => $donation->id]);
    }

    public function test_claimed_listing_cannot_be_removed(): void
    {
        $donor = User::factory()->create(['role' => 'donor']);

        $donation = Donation::create([
            'food_category' => 'Cooked Meals',
            'quantity' => '15 Trays',
            'quantity_kg' => 14,
            'expiry_time' => now()->addDay(),
            'location' => 'Mirpur',
            'status' => 'claimed',
            'donor_id' => $donor->id,
        ]);

        $response = $this
            ->actingAs($donor)
            ->delete(route('donations.destroy', $donation));

        $response->assertForbidden();
        $this->assertDatabaseHas('donations', ['id' => $donation->id]);
    }

    public function test_donor_can_edit_their_available_listing(): void
    {
        $donor = User::factory()->create(['role' => 'donor']);

        $donation = Donation::create([
            'food_category' => 'Cooked Meals',
            'quantity' => '25 Trays',
            'quantity_kg' => 20,
            'expiry_time' => now()->addDay(),
            'location' => 'Mohammadpur',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        $response = $this
            ->actingAs($donor)
            ->patch(route('donations.update', $donation), [
                'food_category' => 'Fresh Produce',
                'quantity' => '18 Crates',
                'quantity_kg' => 22.5,
                'expiry_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'location' => 'Farmgate',
                'special_instructions' => 'Keep refrigerated',
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('success');

        $donation->refresh();

        $this->assertSame('Fresh Produce', $donation->food_category);
        $this->assertSame('18 Crates', $donation->quantity);
        $this->assertSame('Farmgate', $donation->location);
        $this->assertSame('Keep refrigerated', $donation->special_instructions);
    }

    public function test_donor_archive_shows_expired_and_completed_donations(): void
    {
        $donor = User::factory()->create(['role' => 'donor']);
        $receiver = User::factory()->create(['role' => 'receiver']);

        Donation::create([
            'food_category' => 'Bakery Items',
            'quantity' => '10 Packs',
            'quantity_kg' => 6,
            'expiry_time' => now()->subHour(),
            'location' => 'Uttara',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        Donation::create([
            'food_category' => 'Cooked Meals',
            'quantity' => '40 Meals',
            'quantity_kg' => 16,
            'expiry_time' => now()->subDay(),
            'location' => 'Badda',
            'status' => 'completed',
            'donor_id' => $donor->id,
            'receiver_id' => $receiver->id,
            'picked_up_at' => now()->subHours(2),
        ]);

        Donation::create([
            'food_category' => 'Fresh Produce',
            'quantity' => '12 Boxes',
            'quantity_kg' => 14,
            'expiry_time' => now()->addDay(),
            'location' => 'Rampura',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        $response = $this
            ->actingAs($donor)
            ->get(route('donations.archive'));

        $response->assertOk();
        $response->assertSee('Bakery Items');
        $response->assertSee('Cooked Meals');
        $response->assertDontSee('Fresh Produce');
    }

    public function test_donor_can_relist_an_archived_donation(): void
    {
        $donor = User::factory()->create(['role' => 'donor']);

        $donation = Donation::create([
            'food_category' => 'Bakery Items',
            'quantity' => '10 Packs',
            'quantity_kg' => 6,
            'expiry_time' => now()->subHour(),
            'location' => 'Uttara',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        $response = $this
            ->actingAs($donor)
            ->post(route('donations.relist', $donation));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseCount('donations', 2);
        $this->assertDatabaseHas('donations', [
            'food_category' => 'Bakery Items',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);
    }

    public function test_old_image_is_removed_when_donation_image_is_replaced(): void
    {
        Storage::fake('public');

        $donor = User::factory()->create(['role' => 'donor']);
        $oldPath = UploadedFile::fake()->image('old.jpg')->store('donations', 'public');

        $donation = Donation::create([
            'food_category' => 'Cooked Meals',
            'quantity' => '20 Trays',
            'quantity_kg' => 15,
            'expiry_time' => now()->addDay(),
            'location' => 'Mohakhali',
            'status' => 'available',
            'donor_id' => $donor->id,
            'image_path' => $oldPath,
        ]);

        $response = $this
            ->actingAs($donor)
            ->patch(route('donations.update', $donation), [
                'food_category' => 'Cooked Meals',
                'quantity' => '20 Trays',
                'quantity_kg' => 15,
                'expiry_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'location' => 'Mohakhali',
                'special_instructions' => null,
                'image' => UploadedFile::fake()->image('new.jpg'),
            ]);

        $response->assertRedirect(route('dashboard'));
        Storage::disk('public')->assertExists($donation->fresh()->image_path);
        $this->assertNotSame($oldPath, $donation->fresh()->image_path);

        if (PHP_OS_FAMILY !== 'Windows') {
            Storage::disk('public')->assertMissing($oldPath);
        }
    }
}
