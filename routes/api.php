<?php

use App\Http\Controllers\API\OutageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware("auth:sanctum")->group(function () {
    Route::post("/outages", [OutageController::class, "store"]);

    Route::get("/outages", [OutageController::class, "index"]);
});
