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
        // Commands\Inspire::class,
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
        $schedule->command("m3:broadcast")->everyMinute(); // 4gekkman's
        $schedule->command("m5:email_cleartable")->cron("*/15 * * * * *"); // 4gekkman's
        $schedule->command("m5:phone_cleartable")->cron("*/15 * * * * *"); // 4gekkman's
        $schedule->command("m5:delnotverifiedemail")->hourly(); // 4gekkman's
        $schedule->command("m5:delnotverifiedphone")->hourly(); // 4gekkman's
        $schedule->command("m5:auth_clear_expired")->daily(); // 4gekkman's
        $schedule->command("m5:email_authcodes_clear")->cron("*/15 * * * * *"); // 4gekkman's
        $schedule->command("m5:phone_authcodes_clear")->cron("*/15 * * * * *"); // 4gekkman's
        $schedule->command("m11:start")->cron("*/1 * * * * *"); // 4gekkman's
        $schedule->command("m12:update_faq")->withoutOverlapping()->hourly(); // 4gekkman's
        //$schedule->command("m1:run_light")->cron("*/10*****"); // 4gekkman's
        $schedule->command("m14:update_all_bots_goods")->cron("*/15 * * * * *"); // 4gekkman's
        $schedule->command("m14:update_tos_goods")->cron("*/15 * * * * *"); // 4gekkman's
        $schedule->command("m15:reset_statuses")->cron("0 0 * * * *"); // 4gekkman's
        $schedule->command("m15:get_time_until_next_day")->cron("* * * * * *"); // 4gekkman's
        $schedule->command("m9:get_stats_luckyoftheday")->cron("*/5 * * * * *"); // 4gekkman's
        $schedule->command("m9:update_top_cache 1")->cron("*/15 * * * * *"); // 4gekkman's
        $schedule->command("m10:sync_rooms")->cron("*/10 * * * * *"); // 4gekkman's
        $schedule->command("m10:clear_expired_or_extra_messages")->cron("*/10 * * * * *"); // 4gekkman's
        //$schedule->command("m8:update_bots_inventory_count")->cron("*/10*****"); // 4gekkman's
        //$schedule->command("m8:update_bots_authorization_statuses")->cron("*/10*****"); // 4gekkman's
        //$schedule->command("m8:update_bots_apikeys")->hourly(); // 4gekkman's
        //$schedule->command("m8:update_prices_all")->dailyAt("04:00");$schedule->command("m8:update_prices_csgofast")->dailyAt("04:00"); // 4gekkman's
    

    }
}
