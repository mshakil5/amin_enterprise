<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramDestination extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function destinationSlabRate()
    {
        return $this->hasMany(DestinationSlabRate::class);
    }
}
