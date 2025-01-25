<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    protected $table = 'anggaran';
    protected $fillable = ['nama_anggaran', 'persentase_anggaran', 'id_user'];
}
