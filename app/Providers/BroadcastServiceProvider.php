<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //adding ['middleware' => 'auth:sanctum'] causes auth to fail
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
