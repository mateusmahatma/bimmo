<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventOccurrenceStatus extends Model
{
    protected $fillable = [
        'event_id',
        'id_user',
        'occurrence_start',
        'status',
    ];

    protected $casts = [
        'occurrence_start' => 'datetime',
    ];
}

