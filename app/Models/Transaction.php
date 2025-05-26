<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;


    protected $fillable = [
        'client_id',
        'mother_vassel_id',
        'lighter_vassel_id',
        'advance_payment_id',
        'program_id',
        'program_detail_id',
        'vendor_id',
        'challan_no',
        'amount',
        'tran_type',
        'payment_type',
        'date',
        'tran_id',
        'description',
        'ref',
        'table_type',
        'chart_of_account_id',
        'bill_receive_id',
        'bill_number',
        'contact_amount',
        'at_amount',
        'discount',
        'vat_amount',
        'vat_rate',
        'tax_amount',
        'tax_rate',
        'due_amount',
        'other_cost',
        'note',
        'document',
        'equity_id',
        'expense_id',
        'income_id',
        'liablity_id',
        'asset_id',
        'liability_id',
        'status',
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
            ->logExcept(['updated_at', 'created_at'])
            ->setDescriptionForEvent(function(string $eventName) {
                return "Transaction {$this->id} was {$eventName} by " . (auth()->user() ? auth()->user()->name : 'system');
            })
            ->useLogName('transaction');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function motherVassel()
    {
        return $this->belongsTo(MotherVassel::class);
    }

    public function programDetail()
    {
        return $this->belongsTo(ProgramDetail::class);
    }


}
