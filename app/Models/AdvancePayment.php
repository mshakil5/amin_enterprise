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
}
