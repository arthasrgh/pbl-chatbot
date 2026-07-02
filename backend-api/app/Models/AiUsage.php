<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiUsage extends Model
{
    protected $fillable = [
        'nomor',
        'jumlah',
        'tanggal'
    ];
}