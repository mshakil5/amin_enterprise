<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use App\Models\MotherVassel;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TrialBalanceController extends Controller
{
    public function trialBalance(Request $request)
    {

        $startDate = '2025-07-20';
        $endDate = now()->subDay()->toDateString();

        // Asset
        $fixedAssets2 = ChartOfAccount::where('sub_account_head', 'Fixed Asset')->get();
        $currentAssets = ChartOfAccount::where('sub_account_head', 'Current Asset')->get();

        $fixedAssets = DB::table('chart_of_accounts as coa')
                    ->leftJoin('transactions as t', 't.chart_of_account_id', '=', 'coa.id')
                    ->select(
                        'coa.id',
                        'coa.serial', 
                        'coa.account_name',
                        DB::raw("COALESCE(SUM(CASE WHEN t.tran_type = 'Purchase' THEN t.amount ELSE 0 END), 0) AS purchases"),
                        DB::raw("COALESCE(SUM(CASE WHEN t.tran_type = 'Sold' THEN t.amount ELSE 0 END), 0) AS sold"),
                        DB::raw("COALESCE(SUM(CASE WHEN t.tran_type = 'Depreciation' THEN t.amount ELSE 0 END), 0) AS depreciation"),
                        DB::raw("(
                            COALESCE(SUM(CASE WHEN t.tran_type = 'Purchase' THEN t.amount ELSE 0 END), 0)
                            - COALESCE(SUM(CASE WHEN t.tran_type = 'Sold' THEN t.amount ELSE 0 END), 0)
                            - COALESCE(SUM(CASE WHEN t.tran_type = 'Depreciation' THEN t.amount ELSE 0 END), 0)
                        ) AS net")
                    )
                    ->where('coa.account_head', 'Assets')
                    ->where('coa.sub_account_head', 'Fixed Asset')
                    ->groupBy('coa.id', 'coa.serial', 'coa.account_name')
                    ->orderBy('coa.account_name')
                    ->get();

                    // dd($fixedAssets);


        $currentAssets = DB::table('chart_of_accounts as coa')
                    ->leftJoin('transactions as t', 't.chart_of_account_id', '=', 'coa.id')
                    ->select(
                        'coa.id',
                        'coa.serial', 
                        'coa.account_name',
                        DB::raw("COALESCE(SUM(CASE WHEN t.tran_type = 'Received' THEN t.amount ELSE 0 END), 0) AS 	received"),
                        DB::raw("COALESCE(SUM(CASE WHEN t.tran_type = 'Payment' THEN t.amount ELSE 0 END), 0) AS payments"),
                        DB::raw("(
                            COALESCE(SUM(CASE WHEN t.tran_type = 'Received' THEN t.amount ELSE 0 END), 0)
                            - COALESCE(SUM(CASE WHEN t.tran_type = 'Payment' THEN t.amount ELSE 0 END), 0)
                        ) AS net")
                    )
                    ->where('coa.account_head', 'Assets')
                    ->where('coa.sub_account_head', 'Current Asset')
                    ->groupBy('coa.id', 'coa.serial', 'coa.account_name')
                    ->orderBy('coa.account_name')
                    ->get();


                    // dd($currentAssets);
        
        return view('admin.accounts.trial_balance.index', compact('fixedAssets','currentAssets'));
    }
}
