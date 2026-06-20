<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\AstrologyAiInterface;
use App\Services\InterpretationService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AstrologyAiInterface::class, InterpretationService::class);
    }

    public function boot(): void
    {
        //
    }
}