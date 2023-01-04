<?php

namespace App\Console;

use App\Http\Controllers\MessageController;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            // Retrieve all contacts where contract_end_date is within the next 5 days and last_sent_at is null
            $contacts = Contact::where('contract_end_date', '<=', Carbon::now()->addDays(5))
                ->whereNull('last_sent_at')
                ->get();

            foreach ($contacts as $contact) {
                app(MessageController::class)->payReminder($contact->id);
            }
        })->timezone('Asia/Almaty')->dailyAt('23:59');
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
