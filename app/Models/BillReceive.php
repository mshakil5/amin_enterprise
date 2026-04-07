<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillReceive extends Model
{
    use HasFactory;
    use SoftDeletes;

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



    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }



}
