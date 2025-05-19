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
