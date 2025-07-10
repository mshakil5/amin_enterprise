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
      $pettyCash = Transaction::where('tran_type', 'Petty Cash In')->whereDate('date', today())->sum('amount');
      $suspenseAc = 0;
      $liabilities = Transaction::where('table_type', 'Liabilities')
                    ->where('tran_type', 'Received')
                    ->where('payment_type', 'Cash')
                    ->where('account_id', '1')
                    ->whereDate('date', today())
                    ->get();
      $totalReceipts = $liabilities->sum('amount');
      return view('admin.accounts.cash_sheet.index', compact('cashInHand', 'cashInField', 'pettyCash', 'suspenseAc', 'liabilities', 'totalReceipts'));
    }
}
