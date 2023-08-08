<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Redis;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    public function outages() : HasMany
    {
        return $this->hasMany(Outage::class);
    }

    public function lastNonFinishedOutage() : ?Outage
    {
        return $this->outages()->whereNull("end")->latest("start")->first();
    }

    public function emails() : HasMany
    {
        return $this->hasMany(Email::class);
    }

    public function ping() : void
    {
        Redis::set("last-ping:{$this->id}", now()->format("Y-m-d H:i:s"));
    }

    public function lastPing() : ?Carbon
    {
        $ping = Redis::get("last-ping:{$this->id}");
        if($ping === null) {
            return null;
        }

        return Carbon::createFromFormat("Y-m-d H:i:s", $ping);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
