<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChallanRateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'program_detail_id',
        'challan_no',
        'qty',
        'rate_per_unit',
        'total',
        'status',
        'updated_by',
        'created_by',
    ];


    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function programDetail()
    {
        return $this->belongsTo(ProgramDetail::class);
    }


}