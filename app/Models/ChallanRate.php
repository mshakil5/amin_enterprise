<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChallanRate extends Model
{
    use HasFactory;
    use SoftDeletes;

    // app/Models/ChallanRate.php

    protected $fillable = [
        'program_detail_id',
        'challan_no',
        'qty',
        'rate_per_unit',
        'total',
        'status',
        'updated_by',
        'created_by',
        'deleted_at',
    ];

}
