<?php

namespace App\Providers;

use App\Models\Donation;
use App\Policies\DonationPolicy;
use App\Support\WindowsSafeFilesystem;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('testing') && DIRECTORY_SEPARATOR === '\\') {
            $this->app->singleton('files', fn () => new WindowsSafeFilesystem);
            $this->app->singleton(Filesystem::class, fn () => $this->app->make('files'));
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Donation::class, DonationPolicy::class);
    }
}
