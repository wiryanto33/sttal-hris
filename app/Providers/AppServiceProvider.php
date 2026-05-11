<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });

        view()->composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                
                if ($user->hasRole('superadmin')) {
                    $count = \App\Models\Task::where('status', '!=', 'selesai')->count();
                } else {
                    $count = $user->tasks()->where('status', '!=', 'selesai')->count();
                }
                
                $view->with('pendingTasksCount', $count);
            }
        });
    }
}
