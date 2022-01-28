<?php

namespace App\Providers;

use App\Repository\UserRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use App\Contracts\Repository\UserRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Password::defaults(
            fn () => Password::min(8)->letters()
                                    ->numbers()
                                    ->symbols()
                                    ->mixedCase()
                                    ->uncompromised()
        );
    }
}
