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

    public static function getAccountBalance($accountId)
    {
        $increase = self::where('account_id', $accountId)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('table_type', 'Income')->where('tran_type', 'Current');
                })->orWhere(function ($q) {
                    $q->where('table_type', 'Assets')->whereIn('tran_type', ['Received', 'Sold']);
                })->orWhere(function ($q) {
                    $q->where('table_type', 'Liabilities')->where('tran_type', 'Received');
                })->orWhere(function ($q) {
                    $q->where('table_type', 'Equity')->where('tran_type', 'Received');
                })->orWhere(function ($q) {
                    $q->whereNull('table_type')->where('tran_type', 'Transfer')->where('description', 'like', 'Transfer from%');
                });
            })
            ->sum('amount');

        $decrease = self::where('account_id', $accountId)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('table_type', 'Income')->where('tran_type', 'Refund');
                })->orWhere(function ($q) {
                    $q->whereIn('table_type', ['Expenses', 'Cogs'])->where('tran_type', 'Current');
                })->orWhere(function ($q) {
                    $q->where('table_type', 'Assets')->whereIn('tran_type', ['Payment', 'Purchase']);
                })->orWhere(function ($q) {
                    $q->where('table_type', 'Liabilities')->where('tran_type', 'Payment');
                })->orWhere(function ($q) {
                    $q->where('table_type', 'Equity')->where('tran_type', 'Payment');
                })->orWhere(function ($q) {
                    $q->whereNull('table_type')->where('tran_type', 'Transfer')->where('description', 'like', 'Transfer to%');
                })->orWhere(function ($q) {
                    $q->where('table_type', 'Asset')->where('tran_type', 'Petty Cash In');
                })->orWhere(function ($q) {
                    $q->where('table_type', 'Expense')->where('tran_type', 'Wallet');
                });
            })
            ->sum('amount');

        return $increase - $decrease;
    }
    
}
