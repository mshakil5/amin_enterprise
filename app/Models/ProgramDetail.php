<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProgramDetail extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->logExcept(['updated_at'])
            ->setDescriptionForEvent(fn(string $eventName) => "Program detail was {$eventName}")
            ->useLogName('program_detail');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function destinationSlabRate()
    {
        return $this->hasMany(DestinationSlabRate::class);
    }

    public function programDestination()
    {
        return $this->hasOne(ProgramDestination::class);
    }

    public function advancePayment()
    {
        return $this->hasOne(AdvancePayment::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function ghat()
    {
        return $this->belongsTo(Ghat::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function motherVassel()
    {
        return $this->belongsTo(MotherVassel::class);
    }


    public function clientRate()
    {
        return $this->hasMany(ClientRate::class);
    }

    public function challanRate()
    {
        return $this->hasMany(ChallanRate::class);
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class);
    }
}
