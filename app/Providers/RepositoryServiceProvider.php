<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\StudentRepository;
use App\Services\StudentService;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            StudentRepository::class,
            fn () => new StudentRepository(
                app(\App\Models\Student::class)
            )
        );

        $this->app->singleton(
            StudentService::class,
            fn () => new StudentService(
                app(StudentRepository::class)
            )
        );
    }

    public function boot(): void
    {
        //
    }
}