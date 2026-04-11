<?php

namespace App\Notifications;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DonationClaimed extends Notification
{
    use Queueable;

    protected $donation;
    protected $receiver;

    /**
     * Create a new notification instance.
     */
    public function __construct(Donation $donation, User $receiver)
    {
        $this->donation = $donation;
        $this->receiver = $receiver;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'donation_id' => $this->donation->id,
            'message' => "Your donation '{$this->donation->food_category}' has been claimed by {$this->receiver->name}.",
            'action_url' => route('dashboard'),
        ];
    }
}
