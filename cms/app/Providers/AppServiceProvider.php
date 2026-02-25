<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Models\Employee;

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
        // Set default date format for Carbon
        Carbon::setToStringFormat('d-m-Y');
        
        // Share birthday data with header view
        View::composer('auth.layouts.header', function ($view) {
            $todayBirthdays = Cache::remember('header_today_birthdays', 3600, function () {
                return Employee::whereRaw('DATE_FORMAT(dob, "%m-%d") = ?', [date('m-d')])
                    ->whereNotNull('dob')
                    ->select('id', 'first_name', 'last_name', 'full_name', 'department', 'dob')
                    ->get();
            });
            
            $view->with('todayBirthdays', $todayBirthdays);
        });
    }
}
