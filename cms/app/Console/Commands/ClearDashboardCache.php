<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearDashboardCache extends Command
{
    protected $signature = 'cache:clear-dashboard';
    protected $description = 'Clear dashboard cache for better performance';

    public function handle()
    {
        Cache::forget('dashboard_stats');
        Cache::forget('today_birthdays');
        Cache::forget('today_callbacks');
        Cache::forget('active_job_openings');
        
        $this->info('Dashboard cache cleared successfully!');
        return 0;
    }
}
