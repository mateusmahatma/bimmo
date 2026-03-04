<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'id_user',
        'title',
        'description',
        'start_at',
        'end_at',
        'all_day',
        'category',
        'status',
        'color',
        'rrule',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'all_day' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user');
    }
}
