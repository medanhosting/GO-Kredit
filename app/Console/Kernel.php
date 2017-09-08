<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\IGTrackFollowerCount::class,
        \App\Console\Commands\IGAddPokeList::class,
        \App\Console\Commands\IGAnalyzeMedia::class,
        \App\Console\Commands\IGAnalyzeTag::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(Commands\IGTrackFollowerCount::class)->dailyAt('23:00');
        $schedule->command(Commands\IGAddPokeList::class)->hourly();
        $schedule->command(Commands\IGAnalyzeMedia::class)->dailyAt('01:00');
        $schedule->command(Commands\IGAnalyzeMedia::class)->dailyAt('13:00');
        $schedule->command(Commands\IGAnalyzeTag::class)->dailyAt('02:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
