<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'content',
        'is_checked',
        'is_pinned',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
