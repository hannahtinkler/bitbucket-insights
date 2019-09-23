<?php

namespace App\Console;

use App\Models\Setting;
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
        \App\Console\Commands\ImportTeamMembers::class,
        \App\Console\Commands\ImportPullRequests::class,
        \App\Console\Commands\ImportPullRequestApprovals::class,
    ];

    private $minutelyCommands = [
        'import:pull-requests',
        'import:pull-request-approvals',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('import:team-members')->dailyAt('00:00');

        foreach ($this->minutelyCommands as $command) {
            $schedule
                ->command($command)
                ->everyFiveMinutes()
                ->withoutOverlapping()
                ->before(function () {
                    app(Setting::class)->set(Setting::CURRENTLY_REFRESHING, true);
                 })
                 ->after(function () {
                    app(Setting::class)->set(Setting::CURRENTLY_REFRESHING, false);
                    app(Setting::class)->set(Setting::LAST_REFRESH, now()->format('Y-m-d H:i:s'));
                 });
        }
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
