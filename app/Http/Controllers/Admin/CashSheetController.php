<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AdvancePayment;
use App\Models\Transaction;

class CashSheetController extends Controller
{
    public function cashSheet(Request $request)
    {
        $expectedDate = '2025-07-21';
        // $date = now()->subDay()->toDateString();
        $date = $request->searchDate ?? '2025-07-22';
        // opening balamnce
        $previousBalance = $this->cashSheetPreviousBalance($expectedDate);
        $cashInHandOpening = $previousBalance['previousCashInOfficeClosing'];
        $cashInFieldOpening = $previousBalance['previousCashInFieldClosing'];


        // $date = '2025-07-20';
        $suspenseAccount = 94599;


        $pettyCash = 5000.00;

        $liabilitiesInCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Received')
            ->where('payment_type', 'Cash')
            ->whereDate('date', $date)
            ->get();

        $liabilitiesInBank = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Received')
            ->where('payment_type', 'Bank')
            ->whereDate('date', $date)
            ->get();

        $debitTransfer = Transaction::where('tran_type', 'TransferIn')
            ->whereDate('date', $date)->get();

            
        $creditTransfer = Transaction::where('tran_type', 'TransferOut')
            ->whereDate('date', $date)->get();

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

        $vendorAdvances = Transaction::with(['motherVassel', 'programDetail'])
                ->where([
                    ['tran_type', 'Advance'],
                    ['payment_type', 'Cash'],
                ])
                ->whereHas('programDetail', function ($query) use ($date) {
                    $query->whereDate('date', $date);
                })
                ->get()
                ->groupBy('mother_vassel_id');

        return view('admin.accounts.cash_sheet.index', compact(
            'cashInHandOpening','cashInFieldOpening','pettyCash','liabilitiesInCash','liabilitiesInBank','totalReceipts','expenses','totalExpenses','vendorAdvances','date','liabilitiesPaymentInCash','liabilitiesPaymentInBank','suspenseAccount','debitTransfer', 'creditTransfer'
        ));
    }


    public function cashSheetPreviousBalance($expectedDate)
    {
        $startDate = '2025-07-20'; // Example start date
        // Use today's date dynamically
        $date = $expectedDate; // Date before yesterday


        // For previous day's opening balance, we need transactions for $date
        // Initial opening balances (you may need to fetch these from a database or previous day's closing)
        $cashInHandOpening = 347224.00; // Example value; replace with actual data
        $cashInFieldOpening = 281130.00; // Example value; replace with actual data
        $suspenseAccount = 94599;
        $pettyCash = 5000.00;

        // Query transactions for the current day
        $rcvLiabilitiesInOfficeCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Received')
            ->whereBetween('date', [$startDate, $date])
            ->where('account_id', 1)
            ->sum('amount');
        $rcvLiabilitiesInFieldCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Received')
            ->whereBetween('date', [$startDate, $date])
            ->where('account_id', 2)
            ->sum('amount');

        $debitTransferInOfficeCash = Transaction::where('tran_type', 'TransferIn')->where('account_id', 1)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $debitTransferInFieldCash = Transaction::where('tran_type', 'TransferIn')->where('account_id', 2)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        

        $totalDebitOfficeCash = $cashInHandOpening + $rcvLiabilitiesInOfficeCash + $debitTransferInOfficeCash; 
        $totalDebitFieldCash = $cashInFieldOpening + $rcvLiabilitiesInFieldCash + $debitTransferInFieldCash;



            // credit calculation start

        $creditTransferOutOfficeCash = Transaction::where('tran_type', 'TransferOut')->where('account_id', 1)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $creditTransferOutFieldCash = Transaction::where('tran_type', 'TransferOut')->where('account_id', 2)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $pmtLiabilitiesInOfficeCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Payment')
            ->where('account_id', 1)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $pmtLiabilitiesInFieldCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Payment')
            ->where('account_id', 2)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');


        $expensesInOfficeCash = Transaction::with('chartOfAccount')
            ->whereIn('table_type', ['Expenses', 'Expense', 'Cogs'])
            ->whereBetween('date', [$startDate, $date])
            ->where('account_id', 1)
            ->sum('amount');

        $expensesInFieldCash = Transaction::with('chartOfAccount')
            ->whereIn('table_type', ['Expenses', 'Expense', 'Cogs'])
            ->whereBetween('date', [$startDate, $date])
            ->where('account_id', 2)
            ->sum('amount');

        

        $vendorAdvances = Transaction::with(['motherVassel', 'programDetail'])
                ->where([
                    ['tran_type', 'Advance'],
                    ['payment_type', 'Cash'],
                ])
                ->whereHas('programDetail', function ($query) use ($date, $startDate) {
                    $query->whereBetween('date', [$startDate, $date]);
                })
                ->sum('amount');
            // this item always reduce from field cash


        $totalCreditOfficeCash = $pmtLiabilitiesInOfficeCash + $expensesInOfficeCash + $creditTransferOutOfficeCash;
        $totalCreditFieldCash = $pmtLiabilitiesInFieldCash + $expensesInFieldCash + $creditTransferOutFieldCash + $vendorAdvances;

        $previousCashInOfficeClosing = $totalDebitOfficeCash - $totalCreditOfficeCash;
        $previousCashInFieldClosing = $totalDebitFieldCash - $totalCreditFieldCash;

        // dd($previousCashInOfficeClosing, $previousCashInFieldClosing);

        // 18,46,864.0     4,97,030.0 

        return [
            'previousCashInOfficeClosing' => $previousCashInOfficeClosing,
            'previousCashInFieldClosing' => $previousCashInFieldClosing
        ];
        

        
    }
}
