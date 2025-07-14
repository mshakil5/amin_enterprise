<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ChartOfAccount;
use App\Models\Account;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
      if (!(in_array('20', json_decode(auth()->user()->role->permission)))) {
        return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
      }
        if($request->ajax()){
            $transactions = Transaction::with('chartOfAccount')
                ->whereIn('table_type', ['Expenses', 'Cogs']);

        if ($request->filled('start_date')) {
                $endDate = $request->filled('end_date') ? $request->input('end_date') : now()->endOfDay();
                $transactions->whereBetween('date', [
                    $request->input('start_date'),
                    $endDate
                ]);
            }

            if ($request->filled('account_name')) {
                $transactions->whereHas('chartOfAccount', function ($query) use ($request) {
                    $query->where('account_name', $request->input('account_name'));
                });
            }

            $transactions = $transactions->latest()->get();
               
                
            return DataTables::of($transactions)
                ->addColumn('chart_of_account', function ($transaction) {
                    return $transaction->chartOfAccount ? $transaction->chartOfAccount->account_name : $transaction->description;
                })
                ->make(true);
        }
        $accounts = ChartOfAccount::where('account_head', 'Expenses')->get();
        $accountList = Account::latest()->get();
        return view('admin.transactions.expense', compact('accounts', 'accountList'));
    }

    public function voucher(Request $request, $id)
    {
        $data = Transaction::with(['chartOfAccount', 'client'])->where('id', $id)->first();
        // dd($data);
        return view('admin.transactions.expVoucher', compact('data'));
    }

    public function store(Request $request)
    {

        if (empty($request->date)) {
            return response()->json(['status' => 303, 'message' => 'Date Field Is Required..!']);
        }

        if (empty($request->chart_of_account_id)) {
            return response()->json(['status' => 303, 'message' => 'Chart of Account ID Field Is Required..!']);
        }

        if (empty($request->table_type)) {
            return response()->json(['status' => 303, 'message' => 'Table Type Field Is Required..!']);
        }

        if (empty($request->amount)) {
            return response()->json(['status' => 303, 'message' => 'Amount Field Is Required..!']);
        }

        if (empty($request->transaction_type)) {
            return response()->json(['status' => 303, 'message' => 'Transaction Type Field Is Required..!']);
        }

        if ($request->transaction_type !== 'Prepaid Adjust' && empty($request->payment_type)) {
            return response()->json(['status' => 303, 'message' => 'Payment Type Field Is Required..!']);
        }

        $transaction = new Transaction();
        $transaction->tran_id = strtoupper(Str::random(2)) . date('Y') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        $transaction->date = $request->input('date');
        $transaction->chart_of_account_id = $request->input('chart_of_account_id');
        $transaction->account_id = $request->input('account_id') ?? null;
        $transaction->client_id = $request->input('client_id');
        $transaction->table_type = $request->input('table_type');
        $transaction->ref = $request->input('ref');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount');
        $transaction->tax_rate = $request->input('tax_rate');
        $transaction->tax_amount = $request->input('tax_amount');
        $transaction->vat_rate = $request->input('vat_rate');
        $transaction->vat_amount = $request->input('vat_amount');
        $transaction->at_amount = $request->input('at_amount');
        $transaction->tran_type = $request->input('transaction_type');
        $transaction->liability_id = $request->input('payable_holder_id');
        $transaction->payment_type = $request->input('payment_type');
        $transaction->expense_id = $request->input('chart_of_account_id');
        $transaction->mother_vassel_id = $request->input('mother_vassel_id');
        $transaction->created_by = Auth()->user()->id;

        $transaction->save();
        $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

       if ($request->account_id) {
            $account = Account::find($request->account_id);
            if ($account) {
                if ($request->transaction_type === 'Current') {
                    $account->amount -= $request->amount;
                }
                $account->save();
            }
        }

        return response()->json(['status' => 200, 'message' => 'Created Successfully']);

    }

    public function edit($id)
    {
        $transaction = Transaction::findOrFail($id);

        $responseData = [
            'id' => $transaction->id,
            'date' => $transaction->date,
            'chart_of_account_id' => $transaction->chart_of_account_id,
            'client_id' => $transaction->client_id,
            'ref' => $transaction->ref,
            'transaction_type' => $transaction->tran_type,
            'amount' => $transaction->amount,
            'tax_rate' => $transaction->tax_rate,
            'tax_amount' => $transaction->tax_amount,
            'at_amount' => $transaction->at_amount,
            'payment_type' => $transaction->payment_type,
            'description' => $transaction->description,
            'payable_holder_id' => $transaction->liability_id,
            'mother_vassel_id' => $transaction->mother_vassel_id,
            'account_id' => $transaction->account_id,
        ];
        return response()->json($responseData);
    }

    public function update(Request $request, $id)
    {

        if (empty($request->date)) {
            return response()->json(['status' => 303, 'message' => 'Date Field Is Required..!']);
        }

        if (empty($request->chart_of_account_id)) {
            return response()->json(['status' => 303, 'message' => 'Chart of Account ID Field Is Required..!']);
        }

        if (empty($request->amount)) {
            return response()->json(['status' => 303, 'message' => 'Amount Field Is Required..!']);
        }

        if (empty($request->transaction_type)) {
            return response()->json(['status' => 303, 'message' => 'Transaction Type Field Is Required..!']);
        }

        if ($request->transaction_type !== 'Prepaid Adjust' && empty($request->payment_type)) {
            return response()->json(['status' => 303, 'message' => 'Payment Type Field Is Required..!']);
        }

        $transaction = Transaction::find($id);
        $oldAccountId = $transaction->account_id;
        $oldTranType = $transaction->tran_type;
        $oldAmount = $transaction->amount;

        // Reverse old only if type is "Current"
        if ($oldTranType === 'Current' && $oldAccountId) {
            $oldAccount = Account::find($oldAccountId);
            if ($oldAccount) {
                $oldAccount->amount += $oldAmount;
                $oldAccount->save();
            }
        }

        $transaction->date = $request->input('date');
        $transaction->chart_of_account_id = $request->input('chart_of_account_id');
        $transaction->client_id = $request->input('client_id');
        $transaction->ref = $request->input('ref');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount');
        // $transaction->tax_rate = $request->input('tax_rate');
        // $transaction->tax_amount = $request->input('tax_amount');
        $transaction->vat_rate = $request->input('vat_rate');
        $transaction->vat_amount = $request->input('vat_amount');
        $transaction->at_amount = $request->input('at_amount');
        $transaction->tran_type = $request->input('transaction_type');

        if ($request->input('transaction_type') !== 'Due') {
        $transaction->liability_id = null;
        } else {
            $transaction->liability_id = $request->input('payable_holder_id');
        }

        // $transaction->liability_id = $request->input('payable_holder_id');
        // $transaction->payment_type = $request->input('payment_type');
        $transaction->expense_id = $request->input('chart_of_account_id');
        $transaction->mother_vassel_id = $request->input('mother_vassel_id');
        $transaction->updated_by = Auth()->user()->id;

        if ($request->input('transaction_type') === 'Prepaid Adjust') {
            $transaction->tax_rate = null;
            $transaction->tax_amount = null;
            $transaction->payment_type = null;
            $transaction->at_amount = $request->input('amount');
        } else {
            $transaction->tax_rate = $request->input('tax_rate');
            $transaction->tax_amount = $request->input('tax_amount');
            $transaction->payment_type = $request->input('payment_type');
        }

        $transaction->save();

        if ($request->transaction_type === 'Current' && $request->account_id) {
            $newAccount = Account::find($request->account_id);
            if ($newAccount) {
                $newAccount->amount -= $request->amount;
                $newAccount->save();
            }
        }

        return response()->json(['status' => 200, 'message' => 'Updated Successfully']);

    }
    
}
