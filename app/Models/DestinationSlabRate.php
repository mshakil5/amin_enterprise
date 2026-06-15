<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DestinationSlabRate extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'client_id', 'vendor_id', // New
        'destination_id', 'ghat_id', 
        'tier_min_qty', 'tier_max_qty', 'tier_rate', // New
        'maxqty', 'below_rate_per_qty', 'above_rate_per_qty', // Old (keep for safety)
        'title', 'date', 'created_by', 'updated_by'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }


    public function destination()
    {
        return $this->belongsTo(Destination::class);
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
