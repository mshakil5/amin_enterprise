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
}
