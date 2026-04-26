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

        if ($request->ajax()) {
            $transactions = Transaction::with(['chartOfAccount', 'account'])
                ->whereIn('table_type', ['Expenses', 'Cogs'])
                ->whereNotNull('chart_of_account_id');

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
                ->addIndexColumn()
                ->addColumn('chart_of_account', function ($transaction) {
                    if ($transaction->chartOfAccount) {
                        return $transaction->chartOfAccount->account_name;
                    }
                    return '<span class="text-muted">' . $transaction->description . '</span>';
                })
                ->addColumn('accountname', function ($transaction) {
                    if ($transaction->account) {
                        return '<span class="badge badge-light">' . $transaction->account->type . '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('amount_formatted', function ($transaction) {
                    $amount = $transaction->amount;
                    $class = 'text-danger';
                    return '<span class="font-weight-bold ' . $class . '">- ' . number_format($amount, 2) . '</span>';
                })
                
                ->addColumn('ref', function ($transaction) {
                    $tranid = $transaction->tran_id;
                    $class = 'text-success';
                    return '<span class="font-weight-bold ' . $class . '">' . $tranid . '</span>';
                })
                ->addColumn('tran_type_badge', function ($transaction) {
                    $type = $transaction->tran_type;
                    if (!$type) return '<span class="text-muted">N/A</span>';

                    $configs = [
                        'Current'       => ['badge-danger', 'fa-minus-circle'],
                        'Prepaid'       => ['badge-info', 'fa-hourglass-half'],
                        'Prepaid Adjust'=> ['badge-secondary', 'fa-exchange-alt'],
                        'Due'           => ['badge-warning', 'fa-clock'],
                        'Payment'       => ['badge-primary', 'fa-money-check'],
                    ];

                    $config = $configs[$type] ?? ['badge-dark', 'fa-question'];
                    return '<span class="badge ' . $config[0] . '"><i class="fas ' . $config[1] . ' mr-1"></i>' . $type . '</span>';
                })
                ->addColumn('payment_badge', function ($transaction) {
                    if (!$transaction->payment_type) return '<span class="text-muted">-</span>';

                    $type = $transaction->payment_type;
                    $configs = [
                        'Cash'              => ['badge-info', 'fa-money-bill'],
                        'Bank'              => ['badge-primary', 'fa-university'],
                        'Account Payable'   => ['badge-warning', 'fa-file-invoice-dollar'],
                    ];

                    $config = $configs[$type] ?? ['badge-secondary', 'fa-question'];
                    return '<span class="badge ' . $config[0] . '"><i class="fas ' . $config[1] . ' mr-1"></i>' . $type . '</span>';
                })
                ->rawColumns(['chart_of_account', 'accountname', 'amount_formatted', 'tran_type_badge', 'payment_badge','ref'])
                ->make(true);
        }

        $accounts = ChartOfAccount::where('account_head', 'Expenses')->get();
        $payableAccounts = ChartOfAccount::where('sub_account_head', 'Account Payable')->get(['account_name', 'id']);
        $accountList = Account::latest()->get();
        return view('admin.transactions.expense', compact('accounts', 'accountList', 'payableAccounts'));
    }

    public function getSummary(Request $request)
    {
        $baseQuery = Transaction::whereIn('table_type', ['Expenses', 'Cogs']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $totalCurrent = (clone $baseQuery)->where('tran_type', 'Current')->sum('amount');
        $totalPrepaid = (clone $baseQuery)->where('tran_type', 'Prepaid')->sum('amount');
        $totalPrepaidAdjust = (clone $baseQuery)->where('tran_type', 'Prepaid Adjust')->sum('amount');
        $totalDue = (clone $baseQuery)->where('tran_type', 'Due')->sum('amount');
        $netOutflow = $totalCurrent + $totalDue;
        $totalCount = (clone $baseQuery)->count();

        $todayExpense = Transaction::whereIn('table_type', ['Expenses', 'Cogs'])
            ->where('tran_type', 'Current')
            ->whereDate('date', today())
            ->sum('amount');

        return response()->json([
            'total_current' => number_format($totalCurrent, 2),
            'total_prepaid' => number_format($totalPrepaid, 2),
            'total_prepaid_adjust' => number_format($totalPrepaidAdjust, 2),
            'total_due' => number_format($totalDue, 2),
            'net_outflow' => number_format($netOutflow, 2),
            'total_count' => $totalCount,
            'today_expense' => number_format($todayExpense, 2),
        ]);
    }

    public function voucher(Request $request, $id)
    {
        $data = Transaction::with(['chartOfAccount', 'client'])->where('id', $id)->first();
        return view('admin.transactions.expVoucher', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'              => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'table_type'        => 'required|string',
            'amount'            => 'required|numeric|min:0',
            'transaction_type'  => 'required|string',
            'account_id'        => 'required_unless:transaction_type,Prepaid Adjust|nullable',
            'payment_type'      => 'required_unless:transaction_type,Prepaid Adjust|string|nullable',
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
            'employee_id'       => 'nullable|integer',
        ]);

        DB::beginTransaction();

        try {
            $transaction = new Transaction($validated);
            $transaction->created_by = auth()->id();
            $transaction->chart_of_account_id = $validated['chart_of_account_id'];
            $transaction->expense_id = $validated['chart_of_account_id'];
            $transaction->account_id = $request->account_id ?? null;
            $transaction->tran_type = $request->transaction_type ?? null;
            $transaction->save();

            $transaction->tran_id = 'EX' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            if (!empty($validated['account_id'])) {
                $account = Account::find($validated['account_id']);
                if ($account && $validated['transaction_type'] === 'Current') {
                    $account->amount -= $validated['amount'];
                    $account->save();
                }
            }

            DB::commit();

            return response()->json(['status' => 200, 'message' => 'Expense created successfully', 'id' => $transaction->id]);

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
            'chart_of_account_id' => $transaction->chart_of_account_id ?? $transaction->expense_id,
            'client_id' => $transaction->client_id,
            'ref' => $transaction->ref,
            'transaction_type' => $transaction->tran_type,
            'amount' => $transaction->amount,
            'tax_rate' => $transaction->tax_rate,
            'tax_amount' => $transaction->tax_amount,
            'vat_rate' => $transaction->vat_rate,
            'vat_amount' => $transaction->vat_amount,
            'at_amount' => $transaction->at_amount,  // Make sure this is included
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
            // FIX: Cast to float to prevent non-numeric error
            $oldAmount    = (float) ($transaction->amount ?? 0);
            $newAmount    = (float) ($validated['amount'] ?? 0);
            $atamount     = $newAmount - ($validated['vat_amount'] ?? 0) - ($validated['tax_amount'] ?? 0);



            // Reverse old account balance (only if it was a Current expense with account)
            if ($oldTranType === 'Current' && $oldAccountId) {
                $oldAccount = Account::find($oldAccountId);
                if ($oldAccount) {
                    $oldAccount->increment('amount', $oldAmount);
                }
            }

            
            
            $transaction->fill([
                'account_id'         => $request->account_id,
                'date'               => $validated['date'],
                'chart_of_account_id'=> $validated['chart_of_account_id'],
                'client_id'          => $validated['client_id'] ?? null,
                'ref'                => $validated['ref'] ?? null,
                'description'        => $validated['description'] ?? null,
                'amount'             => $newAmount,
                'employee_id'        => $validated['employee_id'] ?? null,
                'vat_rate'           => $validated['vat_rate'] ?? null,
                'vat_amount'         => $validated['vat_amount'] ?? null,
                'at_amount'          => $atamount ?? null,
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
                $transaction->at_amount  = $newAmount; 
                $transaction->account_id = null;
            } else {
                $transaction->tax_rate   = $validated['vat_rate'] ?? null;
                $transaction->tax_amount = $validated['vat_amount'] ?? null;
                $transaction->payment_type = $validated['payment_type'];
                $transaction->at_amount  = $atamount; 
            }

            $transaction->save();

            Log::info('ExpenseController@update - expense update values', [
                'transaction_id' => $transaction->id,
                'new_amount' => $newAmount,
                'at_amount' => $atamount,
                'transaction' => $transaction->toArray(),
            ]);

            // Deduct new account balance (only if it's a Current expense with account)
            if ($validated['transaction_type'] === 'Current' && $validated['account_id']) {
                $newAccount = Account::find($validated['account_id']);
                if ($newAccount) {
                    $newAccount->decrement('amount', $newAmount);
                }
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Expense updated successfully.',
            ]);
        });
    }




}