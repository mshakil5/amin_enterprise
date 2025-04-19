<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelBill extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function petrolPump()
    {
        return $this->belongsTo(PetrolPump::class);
    }
}
