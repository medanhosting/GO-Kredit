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
        \App\Console\Commands\KeluarkanMemorialUntukJurnalPagi::class,
        \App\Console\Commands\PengajuanExpiredChecker::class,
        \App\Console\Commands\HitungDendaAngsuran::class,
        \App\Console\Commands\RollbackTransaction::class,
        \App\Console\Commands\KeluarkanSP::class,
        \App\Console\Commands\Release::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(Commands\KeluarkanSP::class)->dailyAt('11:00');
        $schedule->command(Commands\KeluarkanMemorialUntukJurnalPagi::class)->dailyAt('12:00');
        $schedule->command(Commands\PengajuanExpiredChecker::class)->dailyAt('01:00');
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
