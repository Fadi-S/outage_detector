<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class OutageController extends Controller
{
    public function store(Request $request)
    {
        Redis::get();
    }

    public function index(Request $request)
    {

    }
}
