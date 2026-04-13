<?php

namespace App\Notifications;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DonationCompleted extends Notification
{
    use Queueable;

    public function __construct(protected Donation $donation)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'donation_id' => $this->donation->id,
            'message' => "Pickup for '{$this->donation->food_category}' has been marked as completed.",
            'action_url' => route('dashboard'),
            'type' => 'donation_completed',
        ];
    }
}
