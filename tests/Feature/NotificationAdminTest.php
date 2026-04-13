<?php

namespace Tests\Feature;

use App\Models\Donation;
use App\Models\Report;
use App\Models\User;
use App\Notifications\DonationClaimed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_filter_and_mark_notifications_as_read(): void
    {
        $user = User::factory()->create(['role' => 'donor']);
        $receiver = User::factory()->create(['role' => 'receiver']);
        $donation = Donation::create([
            'food_category' => 'Cooked Meals',
            'quantity' => '10 Trays',
            'quantity_kg' => 8,
            'expiry_time' => now()->addDay(),
            'location' => 'Tejgaon',
            'status' => 'available',
            'donor_id' => $user->id,
        ]);

        $user->notify(new DonationClaimed($donation, $receiver));

        $response = $this
            ->actingAs($user)
            ->get(route('notifications.index', ['filter' => 'unread']));

        $response->assertOk();
        $response->assertSee('claimed');

        $notification = $user->notifications()->first();

        $markReadResponse = $this
            ->actingAs($user)
            ->post(route('notifications.read', $notification));

        $markReadResponse->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_admin_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'organization_name' => 'EcoFeed Operations',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('dashboard'));

        $response->assertRedirect(route('admin.index'));
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $donor = User::factory()->create(['role' => 'donor']);

        $response = $this
            ->actingAs($donor)
            ->get(route('admin.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_hide_and_restore_a_donation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $donor = User::factory()->create(['role' => 'donor']);
        $donation = Donation::create([
            'food_category' => 'Cooked Meals',
            'quantity' => '16 Trays',
            'quantity_kg' => 12,
            'expiry_time' => now()->addDay(),
            'location' => 'Mohakhali',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        $hideResponse = $this
            ->actingAs($admin)
            ->post(route('admin.donations.hide', $donation), [
                'reason' => 'Suspicious duplicate listing',
            ]);

        $hideResponse->assertRedirect();
        $this->assertTrue($donation->fresh()->is_hidden);
        $this->assertSame('Suspicious duplicate listing', $donation->fresh()->moderation_reason);

        $restoreResponse = $this
            ->actingAs($admin)
            ->post(route('admin.donations.restore', $donation));

        $restoreResponse->assertRedirect();
        $this->assertFalse($donation->fresh()->is_hidden);
        $this->assertNull($donation->fresh()->moderation_reason);
    }

    public function test_admin_can_suspend_a_user_and_hide_their_active_listings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $donor = User::factory()->create(['role' => 'donor']);
        $donation = Donation::create([
            'food_category' => 'Fresh Produce',
            'quantity' => '8 Boxes',
            'quantity_kg' => 10,
            'expiry_time' => now()->addDay(),
            'location' => 'Banani',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.users.suspend', $donor), [
                'reason' => 'Repeated fake submissions',
            ]);

        $response->assertRedirect();
        $this->assertTrue($donor->fresh()->isSuspended());
        $this->assertTrue($donation->fresh()->is_hidden);

        $restoreResponse = $this
            ->actingAs($admin)
            ->post(route('admin.users.restore', $donor));

        $restoreResponse->assertRedirect();
        $this->assertFalse($donor->fresh()->isSuspended());
    }

    public function test_user_can_report_a_listing_and_a_user(): void
    {
        $receiver = User::factory()->create(['role' => 'receiver']);
        $donor = User::factory()->create(['role' => 'donor']);
        $donation = Donation::create([
            'food_category' => 'Cooked Meals',
            'quantity' => '6 Trays',
            'quantity_kg' => 7,
            'expiry_time' => now()->addDay(),
            'location' => 'Mirpur',
            'status' => 'available',
            'donor_id' => $donor->id,
        ]);

        $listingResponse = $this
            ->actingAs($receiver)
            ->post(route('reports.donations.store', $donation), [
                'reason' => 'The pickup details look misleading.',
            ]);

        $listingResponse->assertRedirect();

        $userResponse = $this
            ->actingAs($receiver)
            ->post(route('reports.users.store', $donor), [
                'reason' => 'This donor has submitted suspicious listings before.',
            ]);

        $userResponse->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'type' => 'donation',
            'reporter_id' => $receiver->id,
            'donation_id' => $donation->id,
            'reported_user_id' => $donor->id,
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('reports', [
            'type' => 'user',
            'reporter_id' => $receiver->id,
            'reported_user_id' => $donor->id,
            'status' => 'open',
        ]);
    }

    public function test_admin_can_resolve_a_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $reporter = User::factory()->create(['role' => 'receiver']);
        $donor = User::factory()->create(['role' => 'donor']);
        $report = Report::create([
            'type' => 'user',
            'status' => 'open',
            'reporter_id' => $reporter->id,
            'reported_user_id' => $donor->id,
            'reason' => 'Possible duplicate account.',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.reports.resolve', $report), [
                'admin_notes' => 'Reviewed and action taken.',
            ]);

        $response->assertRedirect();
        $this->assertSame('resolved', $report->fresh()->status);
        $this->assertSame('Reviewed and action taken.', $report->fresh()->admin_notes);
        $this->assertNotNull($report->fresh()->resolved_at);
    }
}
