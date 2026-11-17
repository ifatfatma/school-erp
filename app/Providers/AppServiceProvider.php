<?php

namespace App\Providers;

use App\Models\SchoolClass;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantManager::class, function () {
            return new TenantManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::model('class', SchoolClass::class);
        Route::model('academic_session', \App\Models\AcademicSession::class);
        Route::model('academic-session', \App\Models\AcademicSession::class);
    }
}
