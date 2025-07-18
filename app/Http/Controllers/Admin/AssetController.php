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

class AssetController extends Controller
{
    public function index(Request $request)
    {
      if (!(in_array('21', json_decode(auth()->user()->role->permission)))) {
        return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
      }
        if($request->ajax()){
            $transactions = Transaction::with('chartOfAccount')
                ->where('table_type', 'Assets');

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
                    return $transaction->chartOfAccount->account_name;
                })
                ->make(true);
        }
        $accounts = ChartOfAccount::where('account_head', 'Assets')->get();
        $accountList = Account::latest()->get();
        return view('admin.transactions.assets', compact('accounts', 'accountList'));
    }

    public function store(Request $request)
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

        if (empty($request->payment_type) && $request->transaction_type != "Depreciation") {
            return response()->json(['status' => 303, 'message' => 'Payment Type Field Is Required..!']);
        }

        $transaction = new Transaction();
        $transaction->date = $request->input('date');
        $transaction->chart_of_account_id = $request->input('chart_of_account_id');
        $transaction->account_id = $request->input('account_id') ?? null;
        $transaction->table_type = 'Assets';
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
        $transaction->asset_id = $request->input('recivible_holder_id');
        $transaction->created_by = Auth()->user()->id;

        $transaction->save();
        $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        if ($request->account_id) {
            $account = Account::find($request->account_id);
            if ($account) {
                if ($request->transaction_type === 'Received' || $request->transaction_type === 'Sold') {
                    $account->amount += $request->amount;
                } elseif ($request->transaction_type === 'Payment' || $request->transaction_type === 'Purchase') {
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
        $chartOfAccount = ChartOfAccount::find($transaction->chart_of_account_id);
    
        $responseData = [
            'id' => $transaction->id,
            'date' => $transaction->date,
            'chart_of_account_id' => $transaction->chart_of_account_id,
            'chart_of_account_type' => $chartOfAccount ? $chartOfAccount->sub_account_head : null,
            'ref' => $transaction->ref,
            'transaction_type' => $transaction->tran_type,
            'amount' => $transaction->amount,
            'tax_rate' => $transaction->tax_rate,
            'tax_amount' => $transaction->tax_amount,
            'at_amount' => $transaction->at_amount,
            'payment_type' => $transaction->payment_type,
            'description' => $transaction->description,
            'account_id' => $transaction->account_id,
            'payable_holder_id' => $transaction->liability_id,
            'recivible_holder_id' => $transaction->asset_id
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

        if (empty($request->payment_type) && $request->transaction_type != "Depreciation") {
            return response()->json(['status' => 303, 'message' => 'Payment Type Field Is Required..!']);
        }

        $transaction = Transaction::find($id);

        $oldAccountId = $transaction->account_id;
        $oldType = $transaction->tran_type;
        $oldAmount = $transaction->amount;

        if ($oldAccountId) {
            $oldAccount = Account::find($oldAccountId);
            if ($oldAccount) {
                if ($oldType === 'Received' || $oldType === 'Sold') {
                    $oldAccount->amount -= $oldAmount;
                } elseif ($oldType === 'Payment' || $oldType === 'Purchase') {
                    $oldAccount->amount += $oldAmount;
                }
                $oldAccount->save();
            }
        }

        $transaction->date = $request->input('date');
        $transaction->chart_of_account_id = $request->input('chart_of_account_id');
        $transaction->account_id = $request->input('account_id') ?? null;
        $transaction->ref = $request->input('ref');
        $transaction->description = $request->input('description');
        $transaction->amount = $request->input('amount');
        $transaction->tax_rate = $request->input('tax_rate');
        $transaction->tax_amount = $request->input('tax_amount');
        $transaction->vat_rate = $request->input('vat_rate');
        $transaction->vat_amount = $request->input('vat_amount');
        $transaction->at_amount = $request->input('at_amount');
        $transaction->tran_type = $request->input('transaction_type');

        if ($request->input('transaction_type') !== 'Purchase') {
        $transaction->liability_id = null;
        } else {
            $transaction->liability_id = $request->input('payable_holder_id');
        }

        if ($request->input('transaction_type') !== 'Sold') {
        $transaction->liability_id = null;
        } else {
            $transaction->asset_id = $request->input('recivible_holder_id');
        }

        // $transaction->liability_id = $request->input('payable_holder_id');
        $transaction->payment_type = $request->input('payment_type');
        // $transaction->asset_id = $request->input('recivible_holder_id');
        $transaction->updated_by = Auth()->user()->id;

        $transaction->save();

        $newAccountId = $request->account_id;
        $newType = $request->transaction_type;
        $newAmount = $request->amount;

        if ($newAccountId) {
            $newAccount = Account::find($newAccountId);
            if ($newAccount) {
                if ($newType === 'Received' || $newType === 'Sold') {
                    $newAccount->amount += $newAmount;
                } elseif ($newType === 'Payment' || $newType === 'Purchase') {
                    $newAccount->amount -= $newAmount;
                }
                $newAccount->save();
            }
        }

        return response()->json(['status' => 200, 'message' => 'Updated Successfully']);

    }
}
