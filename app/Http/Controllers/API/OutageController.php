<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
                $user->outages()->create([
                    "start" => $lastPing,
                    "end" => now(),
                ]);
            } else {
                $lastOutage->end = now();
                $lastOutage->save();
            }
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
                "duration" => $outage->duration(),
                "done" => $outage->end !== null,
            ])->toArray()
        ];
    }
}
