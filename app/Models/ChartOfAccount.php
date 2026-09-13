<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'chart_of_account_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            if (auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->save();
            }
        });
    }

    protected $fillable = [
        'account_head', 'sub_account_head', 'date', 'account_name', 
        'contingent', 'serial', 'description', 'status', 'created_by', 
        'updated_by', 'petrol_pumps_id' 
    ];

    public function petrolPump()
    {
        return $this->belongsTo(PetrolPump::class, 'petrol_pumps_id');
    }


    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id');
    }

}
