<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'destination_id',
        'ghat_id',
        'date', // <--- Add this
        'minqty',
        'maxqty',
        'below_rate_per_qty',
        'above_rate_per_qty',
        'status',
        'created_by',
        'updated_by',
    ];
}
