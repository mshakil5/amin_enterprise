<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelBill extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function petrolPump()
    {
        return $this->belongsTo(PetrolPump::class);
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

    public function programDetails()
    {
        return $this->hasMany(ProgramDetail::class, 'fuel_bill_id', 'id');
    }

    // --- ADD THESE TWO FUNCTIONS ---

    // Calculate total fuel quantity
    public function getTotalFuelQtyAttribute()
    {
        // Check if relation is already loaded to prevent N+1 queries
        if (!$this->relationLoaded('programDetails')) {
            return 0;
        }
        
        return $this->programDetails->sum(function ($detail) {
            return $detail->advancePayment ? $detail->advancePayment->fuelqty : 0;
        });
    }

    // Calculate total fuel amount
    public function getTotalFuelAmountAttribute()
    {
        // Check if relation is already loaded to prevent N+1 queries
        if (!$this->relationLoaded('programDetails')) {
            return 0;
        }
        
        return $this->programDetails->sum(function ($detail) {
            return $detail->advancePayment ? $detail->advancePayment->fuelamount : 0;
        });
    }
}