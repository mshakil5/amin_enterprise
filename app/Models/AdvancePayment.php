<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdvancePayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function petrolPump()
    {
        return $this->belongsTo(PetrolPump::class);
    }
}
