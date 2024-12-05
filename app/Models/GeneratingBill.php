<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratingBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'header_id',
        'date',
        'truck_number',
        'destination',
        'from_location',
        'to_location',
        'shipping_method',
        'challan_qty',
        'trip_number',
        'trip_qty',
        'before_freight_amount',
        'after_freight_amount',
        'additional_claim',
        'final_trip_amount',
        'remark_by_transporter',
        'rental_mode',
        'mode_of_trip',
        'rate_type',
        'sales_region',
        'wings',
        'lc_no',
        'vessel_name',
        'batch_no',
        'billing_ou',
        'billing_legal_entity',
        'bill_no',
        'transaction_status',
        'updated_by',
        'created_by',
    ];
}
