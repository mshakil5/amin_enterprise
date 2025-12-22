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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                ->addColumn('accountname', function ($transaction) {
                    return $transaction->account ? $transaction->account->type : 'Not Found';
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
        // Validate inputs
        $validated = $request->validate([
            'date'              => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'table_type'        => 'required|string',
            'amount'            => 'required|numeric|min:0',
            'transaction_type'  => 'required|string',
                'account_id'         => 'required_unless:transaction_type,Prepaid Adjust|nullable',
                'payment_type'       => 'required_unless:transaction_type,Prepaid Adjust|string|nullable',
            'client_id'         => 'nullable|exists:clients,id',
            'ref'               => 'nullable|string',
            'description'       => 'nullable|string',
            'tax_rate'          => 'nullable|numeric',
            'tax_amount'        => 'nullable|numeric',
            'vat_rate'          => 'nullable|numeric',
            'vat_amount'        => 'nullable|numeric',
            'at_amount'         => 'nullable|numeric',
            'payable_holder_id' => 'nullable|integer',
            'mother_vassel_id'  => 'nullable|integer',
            'employee_id'  => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            // Create transaction
            $transaction = new Transaction($validated);
            $transaction->created_by = auth()->id();
            $transaction->expense_id = $validated['chart_of_account_id']; 
            $transaction->account_id = $request->account_id ?? null; 
            $transaction->tran_type =  $request->transaction_type ?? null;
            $transaction->save();
            $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            // Update account balance if applicable
            if (!empty($validated['account_id'])) {
                $account = Account::find($validated['account_id']);
                if ($account && $validated['transaction_type'] === 'Current') {
                    $account->amount -= $validated['amount'];
                    $account->save();
                }
            }

            DB::commit();

            return response()->json(['status' => 200, 'message' => 'Transaction created successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
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
            'employee_id' => $transaction->employee_id,
        ];
        return response()->json($responseData);
    }

    public function update(Request $request, $id)
    {
        
        $validated = $request->validate([
            'date'               => 'required|date',
            'chart_of_account_id'=> 'required|exists:chart_of_accounts,id',
            'amount'             => 'required|numeric|min:0',
            'transaction_type'   => 'required|string',
            'account_id'         => 'required_unless:transaction_type,Prepaid Adjust|nullable',
            'payment_type'       => 'required_unless:transaction_type,Prepaid Adjust|string|nullable',
            'client_id'          => 'nullable|exists:clients,id',
            'ref'                => 'nullable|string',
            'description'        => 'nullable|string',
            'employee_id'        => 'nullable|exists:users,id',
            'vat_rate'           => 'nullable|numeric',
            'vat_amount'         => 'nullable|numeric',
            'at_amount'          => 'nullable|numeric',
            'payable_holder_id'  => 'nullable',
            'mother_vassel_id'   => 'nullable',
        ]);

        return DB::transaction(function () use ($validated, $id, $request) {
            
            $transaction = Transaction::findOrFail($id);

            $oldAccountId = $transaction->account_id;
            $oldTranType  = $transaction->tran_type;
            $oldAmount    = $transaction->amount;

            
            if ($oldTranType === 'Current' && $oldAccountId) {
                $oldAccount = Account::find($oldAccountId);
                if ($oldAccount) {
                    $oldAccount->increment('amount', $oldAmount);
                }
            }

            Log::info('account_id:' . $request->account_id );

            
            $transaction->fill([
                'account_id'         => $request->account_id,
                'date'               => $validated['date'],
                'chart_of_account_id'=> $validated['chart_of_account_id'],
                'client_id'          => $validated['client_id'] ?? null,
                'ref'                => $validated['ref'] ?? null,
                'description'        => $validated['description'] ?? null,
                'amount'             => $validated['amount'],
                'employee_id'        => $validated['employee_id'] ?? null,
                'vat_rate'           => $validated['vat_rate'] ?? null,
                'vat_amount'         => $validated['vat_amount'] ?? null,
                'at_amount'          => $validated['at_amount'] ?? null,
                'tran_type'          => $validated['transaction_type'],
                'liability_id'       => $validated['transaction_type'] === 'Due' 
                                        ? ($validated['payable_holder_id'] ?? null) 
                                        : null,
                'expense_id'         => $validated['chart_of_account_id'],
                'mother_vassel_id'   => $validated['mother_vassel_id'] ?? null,
                'updated_by'         => Auth::id(),
            ]);

            
            if ($validated['transaction_type'] === 'Prepaid Adjust') {
                $transaction->tax_rate   = null;
                $transaction->tax_amount = null;
                $transaction->payment_type = null;
                $transaction->at_amount  = $validated['amount'];
            } else {
                $transaction->tax_rate   = $validated['vat_rate'] ?? null;
                $transaction->tax_amount = $validated['vat_amount'] ?? null;
                $transaction->payment_type = $validated['payment_type'];
            }

            $transaction->save();

            
            if ($validated['transaction_type'] === 'Current' && $validated['account_id']) {
                $newAccount = Account::find($validated['account_id']);
                if ($newAccount) {
                    $newAccount->decrement('amount', $validated['amount']);
                }
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Transaction updated successfully.',
            ]);
        });
    }
    
}
