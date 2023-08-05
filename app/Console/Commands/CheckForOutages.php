<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\OutageDetectedNotification;
use Illuminate\Console\Command;

class CheckForOutages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'outages:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for outages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        $users->each(function ($user) {
            $lastPing = $user->lastPing();

            if($lastPing === null) {
                return;
            }

            if($lastPing->diffInSeconds(now()) > 120) {
                $lastOutage = $user->lastNonFinishedOutage();

                if(! $lastOutage) {
                    $lastOutage = $user->outages()->create([
                        "start" => $lastPing,
                        "end" => null,
                    ]);

                    $user->notify(new OutageDetectedNotification($lastOutage));
                }
            }
        });
    }
}
