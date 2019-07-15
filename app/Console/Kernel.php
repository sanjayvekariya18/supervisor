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
        'App\Console\Commands\GenerateHourlyReport',
        'App\Console\Commands\ResetMaxRPM',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('generate_hourly_report')->cron('05 */1 * * *');
        $schedule->command('reset_max_rpm')->everyThirtyMinutes();
        $schedule->command('delete_5min_record')->daily();
        $schedule->command('delete_1hour_record')->daily();
        $schedule->command('send_sms_for_disconnected_machines')->dailyAt('08:30');
        $schedule->command('send_sms_for_disconnected_machines')->dailyAt('20:30');
        $schedule->command('send_hourly_stop_machine_notification')->hourly();
        $schedule->command('send_12hour_report_notification')->dailyAt('09:05');
        $schedule->command('send_12hour_report_notification')->dailyAt('21:05');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
