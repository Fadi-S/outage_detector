<?php

namespace App\Models;

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
}
