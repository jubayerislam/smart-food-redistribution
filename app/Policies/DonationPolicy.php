<?php

namespace App\Policies;

use App\Models\Donation;
use App\Models\User;

class DonationPolicy
{
    public function create(User $user): bool
    {
        return $user->role === 'donor' && ! $user->isSuspended();
    }

    public function update(User $user, Donation $donation): bool
    {
        return $user->role === 'donor'
            && ! $user->isSuspended()
            && $donation->donor_id === $user->id
            && $donation->status === 'available'
            && ! $donation->is_hidden
            && ! $donation->isExpired();
    }

    public function delete(User $user, Donation $donation): bool
    {
        return $user->role === 'donor'
            && ! $user->isSuspended()
            && $donation->donor_id === $user->id
            && $donation->status === 'available'
            && ! $donation->is_hidden;
    }

    public function claim(User $user, Donation $donation): bool
    {
        return $user->role === 'receiver'
            && ! $user->isSuspended()
            && $donation->status === 'available'
            && ! $donation->is_hidden
            && ! $donation->isExpired()
            && $donation->donor_id !== $user->id;
    }

    public function complete(User $user, Donation $donation): bool
    {
        return $user->role === 'donor'
            && ! $user->isSuspended()
            && $donation->donor_id === $user->id
            && $donation->status === 'claimed'
            && ! $donation->is_hidden;
    }

    public function viewArchive(User $user): bool
    {
        return $user->role === 'donor' && ! $user->isSuspended();
    }

    public function relist(User $user, Donation $donation): bool
    {
        return $user->role === 'donor'
            && ! $user->isSuspended()
            && $donation->donor_id === $user->id
            && ! $donation->is_hidden
            && in_array($donation->display_status, ['expired', 'completed'], true);
    }
}
