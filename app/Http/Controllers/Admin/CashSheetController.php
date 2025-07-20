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
        $cashInHand = Account::find(1)->amount ?? 0;
        $cashInField = Account::find(2)->amount ?? 0;

        $date = now()->subDay()->toDateString();


        $pettyCash = Transaction::where('tran_type', 'Petty Cash In')
            ->whereDate('date', $date)
            ->sum('amount');

        $liabilities = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Received')
            ->where('payment_type', 'Cash')
            ->where('account_id', 1)
            ->whereDate('date', $date)
            ->get();

        $totalReceipts = $liabilities->sum('amount');

        $expenses = Transaction::with('chartOfAccount')
            ->whereIn('table_type', ['Expenses', 'Cogs'])
            ->where('payment_type', 'Cash')
            ->where('account_id', 1)
            ->whereDate('date', $date)
            ->get();

        $totalExpenses = $expenses->sum('amount');

        $vendorAdvances = Transaction::with('vendor')
            ->where([
                ['tran_type', 'Advance'],
                ['payment_type', 'Cash'],
                ['table_type', 'AdvancePayment'],
            ])
            ->whereDate('date', $date)
            ->get()
            ->groupBy('vendor_id');

        $totalAdvance = $vendorAdvances->flatten()->sum('amount');

        $totalPayments = $totalExpenses + $totalAdvance;

        $closingCashInHand = $cashInHand + $pettyCash + $totalReceipts - $totalExpenses;
        $closingCashInField = $cashInField - $totalAdvance;

        $grandTotalDebit = $cashInHand + $cashInField + $pettyCash + $totalReceipts;
        $grandTotalCredit = $totalPayments + $closingCashInHand + $closingCashInField;

        return view('admin.accounts.cash_sheet.index', compact(
            'cashInHand','cashInField','pettyCash','liabilities','totalReceipts','expenses','totalExpenses','vendorAdvances','totalAdvance','totalPayments','closingCashInHand','closingCashInField','grandTotalDebit','grandTotalCredit'
        ));
    }
}
