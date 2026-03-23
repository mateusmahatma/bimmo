<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'title',
        'body',
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'id_user');
    }

    public function comments()
    {
        return $this->hasMany(ThreadComment::class , 'thread_id');
    }
}
