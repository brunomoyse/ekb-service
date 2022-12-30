<?php

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $contacts = DB::table('contacts')->get();
            // for each contact, check if the expiration date is within a month + check if already sent
            foreach ($contacts as $contact) {
                if ($contact->expiration_date < Carbon::now()->addMonth() && empty($contact->last_sent_at)) {
                    App::call('App\Http\Controllers\ContactController@payReminder', ['id' => $contact->id]);
                }
            }
        })->dailyAt('12:00');
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
