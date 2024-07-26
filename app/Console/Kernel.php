<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            DB::table('resetpassword')->delete();
        })->everyFiveMinutes();

        $schedule->call(function () {
            $dates = DB::table('requestdates')->get();
            foreach ($dates as $d) {
                $from_date = Carbon::parse(date('Y-m-d', strtotime($d->Time)));
                $through_date = Carbon::now()->format('Y-m-d');

                $shift_difference = $from_date->diffInDays($through_date);

                if ($shift_difference >= 2) {

                    $checkcom = DB::table('users')->where('id', '=', $d->WhoAddedDate)->first();
                    if ($checkcom->role == 'Company') {
                        if ($d->ClientDate == null) {
                            DB::table('requestdates')->where('id', '=', $d->id)->delete();
                        }
                    } else {
                        if ($d->CompanyDate != $d->ClientDate) {
                            DB::table('requestdates')->where('id', '=', $d->id)->delete();
                        }
                    }
                }
            }
        })->everyMinute();
    }


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
