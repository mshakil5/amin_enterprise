<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Vendor extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function programDetail()
    {
        return $this->hasMany(ProgramDetail::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }


    public function note()
    {
        return $this->hasMany(VendorNote::class);
    }

    protected function upcomingNotesCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->note()->where('date', '>', Carbon::now())->count(),
        );
    }

    protected function totalNotesCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->note()->count(),
        );
    }




}
