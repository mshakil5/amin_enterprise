<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function motherVassel()
    {
        return $this->belongsTo(MotherVassel::class);
    }

    public function lighterVassel()
    {
        return $this->belongsTo(LighterVassel::class);
    }

    public function programDetail()
    {
        return $this->hasMany(ProgramDetail::class);
    }

}
