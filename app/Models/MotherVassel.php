<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotherVassel extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
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

    public function programDetail()
    {
        return $this->hasMany(ProgramDetail::class);
    }
}
