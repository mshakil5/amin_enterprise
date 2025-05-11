<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'destination_id',
        'ghat_id',
        'client_id',
        'mother_vassel_id',
        'program_name',
        'start_date',
        'end_date',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $guarded = [];

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
