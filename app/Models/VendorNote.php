<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorNote extends Model
{
    use HasFactory;

    protected $fillable = ['vendor_id', 'date', 'note'];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
