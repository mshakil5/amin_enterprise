<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'client_id',
        'mother_vassel_id',
        'lighter_vassel_id',
        'ghat_id',
        'program_date',
        'description',
        'status',
    ];

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

    public function ghat()
    {
        return $this->belongsTo(Ghat::class);
    }

}
