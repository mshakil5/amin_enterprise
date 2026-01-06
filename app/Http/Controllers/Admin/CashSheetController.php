<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AdvancePayment;
use App\Models\Transaction;
use App\Exports\CashSheetExport;
use Maatwebsite\Excel\Facades\Excel;

class CashSheetController extends Controller
{
    public function cashSheet(Request $request)
    {

        $request->validate([
            'searchDate' => ['nullable', 'date', 'before_or_equal:today'],
        ]);


        // Use the date before the search date (or two days ago if not provided)
        $expectedDate = $request->searchDate
            ? \Carbon\Carbon::parse($request->searchDate)->subDay()->toDateString()
            : now()->subDay(2)->toDateString();
        $date = $request->searchDate ?? now()->subDay()->toDateString();

        // Ensure $expectedDate is not before 2025-07-20
        if (\Carbon\Carbon::parse($expectedDate)->lt(\Carbon\Carbon::parse('2025-07-20'))) {
            $expectedDate = '2025-07-20';
        }

        // dd($expectedDate);
        // opening balance
        $previousBalance = $this->cashSheetPreviousBalance($expectedDate);
        if ($request->searchDate == '2025-07-20') {
            $cashInHandOpening = 347224.00;
            $cashInFieldOpening = 321130.00;
        } else {
            $cashInHandOpening = $previousBalance['previousCashInOfficeClosing'];
            $cashInFieldOpening = $previousBalance['previousCashInFieldClosing'];
        }
        
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

        //equity transaction
        $equityInCashReceived = Transaction::with('chartOfAccount')
            ->where('table_type', 'Equity')
            ->where('tran_type', 'Received')
            ->where('payment_type', 'Cash')
            ->whereDate('date', $date)
            ->get();

        $equityInBankReceived = Transaction::with('chartOfAccount')
            ->where('table_type', 'Equity')
            ->where('tran_type', 'Received')
            ->where('payment_type', 'Bank')
            ->whereDate('date', $date)
            ->get();
        //equity transaction

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
            ->whereDate('date', $date)
            ->get();

        $liabilitiesPaymentInBank = Transaction::with('chartOfAccount')
            ->where('table_type', 'Liabilities')
            ->where('tran_type', 'Payment')
            ->where('payment_type', 'Bank')
            ->whereDate('date', $date)
            ->get();


        // Equity Payment 
        $equityPaymentInCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Equity')
            ->where('tran_type', 'Payment')
            ->where('payment_type', 'Cash')
            ->whereDate('date', $date)
            ->get();
        $equityPaymentInBank = Transaction::with('chartOfAccount')
            ->where('table_type', 'Equity')
            ->where('tran_type', 'Payment')
            ->where('payment_type', 'Bank')
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

        $incomes = Transaction::where('table_type', 'Income')
            ->whereDate('date', $date)
            ->whereIn('tran_type', ['Current', 'Received', 'Wallet'])->get();




        return view('admin.accounts.cash_sheet.index', compact(
            'cashInHandOpening','cashInFieldOpening','pettyCash','liabilitiesInCash','liabilitiesInBank','totalReceipts','expenses','totalExpenses','vendorAdvances','date','liabilitiesPaymentInCash','liabilitiesPaymentInBank','suspenseAccount','debitTransfer', 'creditTransfer','incomes','equityInBankReceived','equityInCashReceived','equityPaymentInBank', 'equityPaymentInCash'
        ));
    }


    public function cashSheetPreviousBalance($expectedDate)
    {
        $startDate = '2025-07-20'; // Example start date
        // Use today's date dynamically
        $date = $expectedDate; // Date before yesterday

        // For previous day's opening balance, we need transactions for $date
        // Initial opening balances (you may need to fetch these from a database or previous day's closing)
        $cashInHandOpening = 347224.00; 
        $cashInFieldOpening = 321130.00; 
        // $cashInFieldOpening = 281130.00;
        $suspenseAccount = 94599;
        $pettyCash = 5000.00;


        /** Transfer transactions */


        $debitTransferInOfficeCash = Transaction::where('tran_type', 'TransferIn')->where('account_id', 1)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $debitTransferInFieldCash = Transaction::where('tran_type', 'TransferIn')->where('account_id', 2)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');


        $creditTransferOutOfficeCash = Transaction::where('tran_type', 'TransferOut')->where('account_id', 1)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $creditTransferOutFieldCash = Transaction::where('tran_type', 'TransferOut')->where('account_id', 2)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

            // dd($debitTransferInOfficeCash, $debitTransferInFieldCash,  $creditTransferOutOfficeCash,  $creditTransferOutFieldCash);



        /** Transfer transactions */

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

            // equity received
        $rcvEquityInOfficeCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Equity')
            ->where('tran_type', 'Received')
            ->whereBetween('date', [$startDate, $date])
            ->where('account_id', 1)
            ->sum('amount');
        $rcvEquityInFieldCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Equity')
            ->where('tran_type', 'Received')
            ->whereBetween('date', [$startDate, $date])
            ->where('account_id', 2)
            ->sum('amount');




        
        $incomesInOfficeCash = Transaction::where('table_type', 'Income')
            ->whereIn('tran_type', ['Current', 'Received', 'Wallet'])
            ->where('account_id', 1)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $incomesInFieldCash = Transaction::where('table_type', 'Income')
            ->whereIn('tran_type', ['Current', 'Received', 'Wallet'])
            ->where('account_id', 2)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $totalDebitOfficeCash = $cashInHandOpening 
                                + $rcvLiabilitiesInOfficeCash 
                                + $debitTransferInOfficeCash 
                                + $incomesInOfficeCash 
                                + $rcvEquityInOfficeCash; 
        $totalDebitFieldCash = $cashInFieldOpening 
                                + $rcvLiabilitiesInFieldCash 
                                + $debitTransferInFieldCash 
                                + $incomesInFieldCash 
                                + $rcvEquityInFieldCash;



        // credit calculation start

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

            // Equity payment
        $pmtEquityInOfficeCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Equity')
            ->where('tran_type', 'Payment')
            ->where('account_id', 1)
            ->whereBetween('date', [$startDate, $date])
            ->sum('amount');

        $pmtEquityInFieldCash = Transaction::with('chartOfAccount')
            ->where('table_type', 'Equity')
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


        $totalCreditOfficeCash = $pmtLiabilitiesInOfficeCash 
                                    + $expensesInOfficeCash 
                                    + $creditTransferOutOfficeCash 
                                    + $pmtEquityInOfficeCash;
        $totalCreditFieldCash = $pmtLiabilitiesInFieldCash 
                                    + $expensesInFieldCash 
                                    + $creditTransferOutFieldCash 
                                    + $vendorAdvances 
                                    + $pmtEquityInFieldCash;

                                // dd(
                                //     "totalDebitOfficeCash", $totalDebitOfficeCash, 
                                //     "totalCreditOfficeCash", $totalCreditOfficeCash, 
                                //     "totalDebitFieldCash", $totalDebitFieldCash, 
                                //     "totalCreditFieldCash", $totalCreditFieldCash, 
                                // );

        $previousCashInOfficeClosing = $totalDebitOfficeCash - $totalCreditOfficeCash;
        $previousCashInFieldClosing = $totalDebitFieldCash - $totalCreditFieldCash;

                                // dd(
                                //     "previousCashInOfficeClosing", $previousCashInOfficeClosing, 
                                //     "previousCashInFieldClosing", $previousCashInFieldClosing, 
                                // );



        return [
            'previousCashInOfficeClosing' => $previousCashInOfficeClosing,
            'previousCashInFieldClosing' => $previousCashInFieldClosing
        ];
        

        
    }

    public function downloadExcel(Request $request)
    {
        try {
            $expectedDate = $request->searchDate
                ? \Carbon\Carbon::parse($request->searchDate)->subDay()->toDateString()
                : now()->subDay(2)->toDateString();
            $date = $request->searchDate ?? now()->subDay()->toDateString();

            if (\Carbon\Carbon::parse($expectedDate)->lt(\Carbon\Carbon::parse('2025-07-20'))) {
                $expectedDate = '2025-07-20';
            }

            $previousBalance = $this->cashSheetPreviousBalance($expectedDate);
            $cashInHandOpening = floatval($previousBalance['previousCashInOfficeClosing'] ?? 0);
            $cashInFieldOpening = floatval($previousBalance['previousCashInFieldClosing'] ?? 0);
            $suspenseAccount = 94599.00;
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

            $data = [
                'date' => $date,
                'cashInHandOpening' => $cashInHandOpening,
                'cashInFieldOpening' => $cashInFieldOpening,
                'pettyCash' => $pettyCash,
                'suspenseAccount' => $suspenseAccount,
                'liabilitiesInCash' => $liabilitiesInCash,
                'liabilitiesInBank' => $liabilitiesInBank,
                'totalReceipts' => $liabilitiesInCash->sum('amount') + $liabilitiesInBank->sum('amount'),
                'expenses' => $expenses,
                'totalExpenses' => $expenses->sum('amount'),
                'vendorAdvances' => $vendorAdvances,
                'liabilitiesPaymentInCash' => $liabilitiesPaymentInCash,
                'liabilitiesPaymentInBank' => $liabilitiesPaymentInBank,
                'debitTransfer' => $debitTransfer,
                'creditTransfer' => $creditTransfer,
                'totalBankCredits' => 0,
            ];


            return Excel::download(new CashSheetExport($data), 'Cash_Sheet_' . $date . '.xlsx');
        } catch (\Exception $e) {
            // \Log::error('Excel Export Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate Excel file.'], 500);
        }
    }
}
