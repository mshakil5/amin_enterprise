<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;

class CashSheetController extends Controller
{
    public function cashSheet()
    {
        // $cashInHand = Account::find(1)->amount ?? 0;
        // $cashInField = Account::find(2)->amount ?? 0;
        
        
        $cashInHandOpening = 347224.00;
        $cashInFieldOpening = 281130.00;

        $date = '2025-07-20';
        $suspenseAccount = 94599;
        // $date = now()->subDay()->toDateString();


        $pettyCash = 5000.00;

        $liabilitiesInCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Received')
            ->where('payment_type', 'Cash')
            ->where('account_id', 1)
            ->whereDate('date', $date)
            ->get();

        $liabilitiesInBank = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Received')
            ->where('payment_type', 'Bank')
            ->where('account_id', 1)
            ->whereDate('date', $date)
            ->get();


        $totalReceipts = $liabilitiesInCash->sum('amount') + $liabilitiesInBank->sum('amount');

        // liability payments

        $liabilitiesPaymentInCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Payment')
            ->where('payment_type', 'Cash')
            ->where('account_id', 1)
            ->whereDate('date', $date)
            ->get();

        $liabilitiesPaymentInBank = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Payment')
            ->where('payment_type', 'Bank')
            ->where('account_id', 1)
            ->whereDate('date', $date)
            ->get();

        $expenses = Transaction::with('chartOfAccount')
            ->whereIn('table_type', ['Expenses', 'Expense', 'Cogs'])
            ->whereDate('date', $date)
            ->get();

        $totalExpenses = $expenses->sum('amount');

        $vendorAdvances = Transaction::with('motherVassel')
            ->where([
                ['tran_type', 'Advance'],
                ['payment_type', 'Cash'],
            ])
            ->whereDate('date', $date)
            ->get()
            ->groupBy('mother_vassel_id');

        return view('admin.accounts.cash_sheet.index', compact(
            'cashInHandOpening','cashInFieldOpening','pettyCash','liabilitiesInCash','liabilitiesInBank','totalReceipts','expenses','totalExpenses','vendorAdvances','date','liabilitiesPaymentInCash','liabilitiesPaymentInBank','suspenseAccount'
        ));
    }
}
