<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AdvancePayment extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;


    protected $fillable = [
        'vendor_id',
        'fuelqty',
        'fuel_rate',
        'fueltoken',
        'fuelamount',
        'amount',
        'date',
        'petrol_pump_id',
        'cashamount',
        'receiver_name',
        'payment_type',
        'program_id',
        'program_detail_id',
        'client_id',
        'status',
        'updated_by',
        'created_by',
        'deleted_at',
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at', 'created_at'])
            ->setDescriptionForEvent(function(string $eventName) {
                return "Advance Payment {$this->id} was {$eventName} by " . 
                      (auth()->user() ? auth()->user()->name : 'system');
            })
            ->useLogName('advance_payment');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function petrolPump()
    {
        return $this->belongsTo(PetrolPump::class);
    }

    public function programDetail()
    {
        return $this->hasOne(ProgramDetail::class, 'id', 'program_detail_id');
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
