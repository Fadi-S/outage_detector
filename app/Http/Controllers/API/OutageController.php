<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Notifications\OutageFinishedNotification;
use Illuminate\Http\Request;

class OutageController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();

        $lastPing = $user->lastPing();
        if (!$lastPing) {
            $user->ping();

            return response()->json([
                "message" => "Ping recorded",
            ]);
        }

        if ($lastPing->diffInSeconds(now()) > 120) {
            $lastOutage = $user->lastNonFinishedOutage();

            if (!$lastOutage) {
                $lastOutage = $user->outages()->create([
                    "start" => $lastPing,
                    "end" => now(),
                ]);
            } else {
                $lastOutage->end = now();
                $lastOutage->save();
            }

            $user->notify(new OutageFinishedNotification($lastOutage));
            $user->emails->each(fn($email) => $email->notify(new OutageFinishedNotification($lastOutage)));
        }

        $user->ping();

        return response()->json([
            "message" => "Ping recorded",
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user()->load("outages");

        return [
            "outages" => $user->outages->map(fn($outage) => [
                "start" => $outage->start->format("Y-m-d H:i:s"),
                "end" => $outage->end?->format("Y-m-d H:i:s"),
                "duration" => $outage->duration()->format("%H:%I:%S"),
                "duration_for_humans" => $outage->duration()->forHumans(),
                "done" => $outage->end !== null,
            ])->toArray()
        ];
    }
}
