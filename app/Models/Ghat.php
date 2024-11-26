<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ghat extends Model
{
    use HasFactory;

    public function destinationSlabRate()
    {
        return $this->hasMany(DestinationSlabRate::class);
    }
}
