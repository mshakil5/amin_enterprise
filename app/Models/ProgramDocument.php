<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id', 
        'program_id', 
        'document', 
        'total_truck', 
        'total_challan', 
        'date', 
        'truck_numbers', 
        'mother_vassel_id', 
        'client_id', 
        'created_by', 
        'updated_by', 
        'deleted_by'
    ];

    // Add relationships as needed based on your existing models
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function motherVassel()
    {
        return $this->belongsTo(MotherVassel::class, 'mother_vassel_id');
    }
}