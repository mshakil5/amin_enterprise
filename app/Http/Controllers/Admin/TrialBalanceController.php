<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use App\Models\MotherVassel;
use App\Models\Transaction;

class TrialBalanceController extends Controller
{
    public function trialBalance(Request $request)
    {

        $startDate = '2025-07-20';
        $endDate = now()->subDay()->toDateString();

        // Asset
        $fixedAssets = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->get();
        $currentAssets = ChartOfAccount::where('sub_account_head', 'Current Asset')->get();
        
        return view('admin.accounts.trial_balance.index');
    }
}
