<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function destinationSlabRate()
    {
        return $this->hasMany(DestinationSlabRate::class);
    }

    public function programDetail()
    {
        return $this->hasMany(ProgramDetail::class);
    }

    

}
