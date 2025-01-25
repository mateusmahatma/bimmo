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
        return $this->hasMany(BayarPinjaman::class, 'id_user');
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
    ];
}
