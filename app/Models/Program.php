<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Program extends Model
{
    use HasFactory, LogsActivity , SoftDeletes;

    protected $fillable = [
        'client_id',
        'mother_vassel_id',
        'lighter_vassel_id',
        'ghat_id',
        'program_date',
        'description',
        'status',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Program was {$eventName}")
            ->useLogName('program');
    }

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
