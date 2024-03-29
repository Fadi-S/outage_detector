<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Outage extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = [];
    protected $casts = [
        "start" => "datetime",
        "end" => "datetime",
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function duration() : CarbonInterval
    {
        return $this->start->diffAsCarbonInterval($this->end ?? now());
    }

    public function time() : Attribute
    {
        return Attribute::make(
            get: fn() => $this->duration()->forHumans(),
        );
    }
}
