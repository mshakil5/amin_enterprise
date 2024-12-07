<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public function destination()
    {
        return $this->hasMany(Destination::class);
    }

    public function program()
    {
        return $this->hasMany(Program::class);
    }

    public function clientRate()
    {
        return $this->hasMany(ClientRate::class);
    }
}
