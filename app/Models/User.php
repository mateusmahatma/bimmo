<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    public function retrieveByCredentials(array $credentials)
    {
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (!Str::contains($key, 'password')) {
                $query->whereRaw("BINARY $key = BINARY ?", [$value]);
            }
        }

        return $query->first();
    }

    // Relasi ke model BayarPinjaman
    public function pembayaranPinjaman()
    {
        return $this->hasMany(BayarPinjaman::class , 'id_user');
    }

    // guarded kebalikan dengan fillable, guarded yang tidak boleh diisi
    protected $guarded = ['id'];

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
        'email_verified_at' => 'datetime',
        'name' => 'encrypted',
        'email' => 'encrypted',
        'no_hp' => 'encrypted',
        'nominal_target_dana_darurat' => 'encrypted',
        'profile_photo' => 'encrypted',
        'daily_notification' => 'boolean',
        'notification_interval' => 'integer',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'subscription_auto_renew' => 'boolean',
    ];

    public function isOnTrial()
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }

    public function isSubscribed()
    {
        return $this->subscription_status == 1 && $this->subscription_ends_at && now()->lt($this->subscription_ends_at);
    }

    public function canAccessFeatures()
    {
        return $this->isOnTrial() || $this->isSubscribed();
    }

    public function getRemainingDays()
    {
        if ($this->isSubscribed()) {
            return (int)now()->diffInDays($this->subscription_ends_at);
        }
        if ($this->isOnTrial()) {
            return (int)now()->diffInDays($this->trial_ends_at);
        }
        return 0;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if ($user->isDirty('name')) {
                $user->name_hash = hash('sha256', (string)$user->name);
            }
            if ($user->isDirty('email')) {
                $user->email_hash = hash('sha256', (string)$user->email);
            }
            if ($user->isDirty('no_hp')) {
                $user->no_hp_hash = hash('sha256', (string)$user->no_hp);
            }
        });
    }
}
