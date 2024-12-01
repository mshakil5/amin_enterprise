<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramDetail extends Model
{
    use HasFactory;

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function destinationSlabRate()
    {
        return $this->hasMany(DestinationSlabRate::class);
    }

    public function programDestination()
    {
        return $this->hasOne(ProgramDestination::class);
    }

    public function advancePayment()
    {
        return $this->hasOne(AdvancePayment::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
