<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_receive_id',
        'bill_nos',
        'cheque_number',
        'cheque_date',
        'bank_name',
        'cheque_amount',
        'document_path',
        'document_name',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'bill_nos' => 'array',
        'cheque_date' => 'date',
        'cheque_amount' => 'decimal:2',
    ];

    public function billReceive()
    {
        return $this->belongsTo(BillReceive::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}