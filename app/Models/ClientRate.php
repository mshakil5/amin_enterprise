<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRate extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function ghat()
    {
        return $this->belongsTo(Ghat::class);
    }

    
}