<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'vendor_id',          // New
        'program_id', 'date', 'title', 
        'destination_id', 'ghat_id', 
        'tier_min_qty', 'tier_max_qty', 'tier_rate', // New
        'minqty', 'maxqty', 'below_rate_per_qty', 'above_rate_per_qty', // Old
        'status',
        'updated_by', 'created_by'
    ];

    // Add relationships if you haven't already
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class); // Make sure Vendor model exists
    }


}
