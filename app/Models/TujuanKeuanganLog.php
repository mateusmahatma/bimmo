<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TujuanKeuanganLog extends Model
{
    use HasFactory;

    protected $table = 'tujuan_keuangan_log';
    protected $primaryKey = 'id_log';
    protected $guarded = ['id_log'];

    /**
     * Get the goal that owns this log entry.
     */
    public function goal()
    {
        return $this->belongsTo(TujuanKeuangan::class , 'id_tujuan_keuangan');
    }
}
