<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'id_user',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class , 'thread_id');
    }
}
