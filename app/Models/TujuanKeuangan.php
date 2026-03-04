<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TujuanKeuangan extends Model
{
    use HasFactory;

    protected $table = 'tujuan_keuangan';
    protected $primaryKey = 'id_tujuan_keuangan';
    protected $guarded = ['id_tujuan_keuangan'];

    /**
     * Get the user that owns the financial goal.
     */
    public function user()
    {
        return $this->belongsTo(User::class , 'id_user');
    }
}
