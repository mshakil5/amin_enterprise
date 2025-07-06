<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Destination extends Model
{
    use HasFactory;
    use SoftDeletes;

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

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id(); // Set the ID of the authenticated user
                $model->save();
            }
        });
    }

}
