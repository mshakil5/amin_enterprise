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

    protected $fillable = [
        'date',
        'programid',
        'consignmentno',
        'program_id',
        'fuel_bill_id',
        'mother_vassel_id',
        'lighter_vassel_id',
        'client_id',
        'dest_status',
        'tran_status',
        'after_date',
        'vendor_sequence_number_id',
        'destination_id',
        'ghat_id',
        'vendor_id',
        'truck_number',
        'headerid',
        'old_qty',
        'dest_qty',
        'challan_no',
        'line_charge',
        'scale_fee',
        'other_cost',
        'transportcost',
        'carrying_bill',
        'old_carrying_bill',
        'additional_cost',
        'advance',
        'due',
        'rate_status',
        'status',
        'note',
        'bill_no',
        'generate_bill',
        'bill_status',
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
