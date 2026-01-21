<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorSequenceNumber extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

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
        return $this->hasMany(ProgramDetail::class, 'vendor_sequence_number_id', 'id');
    }

    public function programDetailsCount()
    {
        return $this->programDetail()->count();
    }
    
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'vendor_sequence_number_id', 'id');
    }

    public function getAdvancePaymentTotals()
    {
        $totals = $this->programDetail()
            ->with('advancePayment')
            ->get()
            ->reduce(function ($carry, $programDetail) {
                $advancePayment = $programDetail->advancePayment;

                return [
                    'total_fuelqty' => $carry['total_fuelqty'] + ($advancePayment->fuelqty ?? 0),
                    'total_fuelamount' => $carry['total_fuelamount'] + ($advancePayment->fuelamount ?? 0),
                    'total_cashamount' => $carry['total_cashamount'] + ($advancePayment->cashamount ?? 0),
                ];
            }, [
                'total_fuelqty' => 0,
                'total_fuelamount' => 0,
                'total_cashamount' => 0,
            ]);

        return $totals;
    }
}
