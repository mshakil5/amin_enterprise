<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramDestination extends Model
{
    use HasFactory;

    public function destinationSlabRate()
    {
        return $this->hasMany(DestinationSlabRate::class);
    }
}
