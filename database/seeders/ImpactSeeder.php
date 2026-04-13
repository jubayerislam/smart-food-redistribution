<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImpactSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a Donor
        $donor = User::create([
            'name' => 'Green Valley Hotel',
            'email' => 'donor@example.com',
            'password' => 'password',
            'role' => 'donor',
            'organization_name' => 'Green Valley Hotels Group',
        ]);

        // 2. Create an NGO
        $ngo = User::create([
            'name' => 'City Charity',
            'email' => 'receiver@example.com',
            'password' => 'password',
            'role' => 'receiver',
            'organization_name' => 'City Hope Foundation',
        ]);

        User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin',
            'organization_name' => 'EcoFeed Operations',
        ]);

        // 3. Create Donations across months
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May'];
        $weights = [45.5, 62.0, 38.5, 82.0, 55.5];

        foreach ($months as $index => $monthName) {
            Donation::create([
                'donor_id' => $donor->id,
                'receiver_id' => ($index % 2 === 0) ? $ngo->id : null,
                'food_category' => 'Cooked Meals',
                'quantity' => '50 Servings',
                'quantity_kg' => $weights[$index],
                'expiry_time' => now()->addDays(5),
                'location' => 'Downtown Central',
                'status' => ($index % 2 === 0) ? 'completed' : 'available',
                'created_at' => now()->subMonths(5 - $index),
            ]);
        }
    }
}
