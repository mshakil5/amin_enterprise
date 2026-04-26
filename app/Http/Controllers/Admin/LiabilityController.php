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

class LiabilityController extends Controller
{
    public function index(Request $request)
    {
        if (!(in_array('22', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        
        if ($request->ajax()) {
            $transactions = Transaction::with(['chartOfAccount', 'account'])
                ->where('table_type', 'Liabilities')
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
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('accountname', function ($transaction) {
                    if ($transaction->account) {
                        return '<span class="badge badge-light">' . $transaction->account->type . '</span>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('amount_formatted', function ($transaction) {
                    $amount = $transaction->amount;
                    if ($transaction->tran_type === 'Payment') {
                        $class = 'text-danger';
                        $prefix = '-';
                    } else {
                        $class = 'text-success';
                        $prefix = '';
                    }
                    return '<span class="font-weight-bold ' . $class . '">' . $prefix . number_format(abs($amount), 2) . '</span>';
                })
                ->addColumn('tran_type_badge', function ($transaction) {
                    $type = $transaction->tran_type;
                    if (!$type) return '<span class="text-muted">N/A</span>';
                    
                    $configs = [
                        'Received' => ['badge-success', 'fa-arrow-down'],
                        'Payment'  => ['badge-danger', 'fa-arrow-up'],
                    ];
                    
                    $config = $configs[$type] ?? ['badge-warning', 'fa-exchange-alt'];
                    return '<span class="badge ' . $config[0] . '"><i class="fas ' . $config[1] . ' mr-1"></i>' . $type . '</span>';
                })
                ->addColumn('payment_badge', function ($transaction) {
                    if (!$transaction->payment_type) return '<span class="text-muted">-</span>';
                    
                    $type = $transaction->payment_type;
                    $configs = [
                        'Cash' => ['badge-info', 'fa-money-bill'],
                        'Bank' => ['badge-primary', 'fa-university'],
                    ];
                    
                    $config = $configs[$type] ?? ['badge-secondary', 'fa-question'];
                    return '<span class="badge ' . $config[0] . '"><i class="fas ' . $config[1] . ' mr-1"></i>' . $type . '</span>';
                })
                ->rawColumns(['chart_of_account', 'accountname', 'amount_formatted', 'tran_type_badge', 'payment_badge'])
                ->make(true);
        }
        
        $accounts = ChartOfAccount::where('account_head', 'Liabilities')->get();
        $accountList = Account::latest()->get();
        return view('admin.transactions.liabilities', compact('accounts', 'accountList'));
    }

    public function getSummary(Request $request)
    {
        // FIX: Use clone instead of re-querying
        $baseQuery = Transaction::where('table_type', 'Liabilities');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $totalReceived = (clone $baseQuery)->where('tran_type', 'Received')->sum('amount');
        $totalPayment = (clone $baseQuery)->where('tran_type', 'Payment')->sum('amount');
        $netBalance = $totalReceived - $totalPayment;
        $totalCount = (clone $baseQuery)->count();

        $todayQuery = Transaction::where('table_type', 'Liabilities')->whereDate('date', today());
        $todayReceived = (clone $todayQuery)->where('tran_type', 'Received')->sum('amount');
        $todayPayment = (clone $todayQuery)->where('tran_type', 'Payment')->sum('amount');

        return response()->json([
            'total_received' => number_format($totalReceived, 2),
            'total_payment' => number_format($totalPayment, 2),
            'net_balance' => number_format($netBalance, 2),
            'total_count' => $totalCount,
            'today_received' => number_format($todayReceived, 2),
            'today_payment' => number_format($todayPayment, 2),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:Received,Payment',
            'payment_type' => 'required|in:Cash,Bank',
        ], [
            'date.required' => 'Date field is required',
            'chart_of_account_id.required' => 'Chart of Account is required',
            'amount.required' => 'Amount field is required',
            'transaction_type.required' => 'Transaction Type is required',
            'payment_type.required' => 'Payment Type is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 303, 'message' => $validator->errors()->first()]);
        }

        DB::beginTransaction();

        try {
            $transaction = new Transaction();
            $transaction->date = $request->input('date');
            $transaction->chart_of_account_id = $request->input('chart_of_account_id');
            $transaction->account_id = $request->input('account_id') ?? null;
            $transaction->table_type = 'Liabilities';
            $transaction->ref = $request->input('ref');
            $transaction->description = $request->input('description');
            $transaction->amount = $request->input('amount');
            $transaction->tax_rate = $request->input('tax_rate');
            $transaction->tax_amount = $request->input('tax_amount');
            $transaction->vat_rate = $request->input('vat_rate');
            $transaction->vat_amount = $request->input('vat_amount');
            $transaction->at_amount = $request->input('at_amount');
            $transaction->tran_type = $request->input('transaction_type');
            $transaction->payment_type = $request->input('payment_type');
            $transaction->liablity_id = $request->input('chart_of_account_id');
            $transaction->created_by = auth()->user()->id;

            $transaction->save();
            $transaction->tran_id = 'LI' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            // FIX: Cast to float, use increment/decrement
            $amount = (float) $request->input('amount');
            $transType = $request->input('transaction_type');

            if ($transaction->account_id) {
                $account = Account::find($transaction->account_id);
                if ($account) {
                    if ($transType === 'Received') {
                        $account->increment('amount', $amount);
                    } elseif ($transType === 'Payment') {
                        $account->decrement('amount', $amount);
                    }
                }
            }

            DB::commit();

            return response()->json(['status' => 200, 'message' => 'Liability created successfully', 'id' => $transaction->id]);

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
            'tran_id' => $transaction->tran_id,  // FIX: Added tran_id
            'date' => $transaction->date,
            'chart_of_account_id' => $transaction->chart_of_account_id,
            'ref' => $transaction->ref,
            'tran_type' => $transaction->tran_type,
            'amount' => $transaction->amount,
            'tax_rate' => $transaction->tax_rate,
            'tax_amount' => $transaction->tax_amount,
            'at_amount' => $transaction->at_amount,
            'payment_type' => $transaction->payment_type,
            'description' => $transaction->description,
            'account_id' => $transaction->account_id,
        ];
        return response()->json($responseData);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:Received,Payment',
            'payment_type' => 'required|in:Cash,Bank',
        ], [
            'date.required' => 'Date field is required',
            'chart_of_account_id.required' => 'Chart of Account is required',
            'amount.required' => 'Amount field is required',
            'transaction_type.required' => 'Transaction Type is required',
            'payment_type.required' => 'Payment Type is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 303, 'message' => $validator->errors()->first()]);
        }

        return DB::transaction(function () use ($request, $id) {

            $transaction = Transaction::findOrFail($id);

            // FIX: Cast to float
            $oldAccountId = $transaction->account_id;
            $oldType = $transaction->tran_type;
            $oldAmount = (float) ($transaction->amount ?? 0);

            $newType = $request->input('transaction_type');
            $newAmount = (float) ($request->input('amount') ?? 0);

            // Reverse old account balance
            if ($oldAccountId) {
                $oldAccount = Account::find($oldAccountId);
                if ($oldAccount) {
                    if ($oldType === 'Received') {
                        $oldAccount->decrement('amount', $oldAmount);
                    } elseif ($oldType === 'Payment') {
                        $oldAccount->increment('amount', $oldAmount);
                    }
                }
            }

            // Update transaction
            $transaction->date = $request->input('date');
            $transaction->chart_of_account_id = $request->input('chart_of_account_id');
            $transaction->account_id = $request->input('account_id') ?? null;
            $transaction->ref = $request->input('ref');
            $transaction->description = $request->input('description');
            $transaction->amount = $newAmount;
            $transaction->tax_rate = $request->input('tax_rate');
            $transaction->tax_amount = $request->input('tax_amount');
            $transaction->vat_rate = $request->input('vat_rate');
            $transaction->vat_amount = $request->input('vat_amount');
            $transaction->at_amount = $request->input('at_amount');
            $transaction->tran_type = $newType;
            $transaction->payment_type = $request->input('payment_type');
            $transaction->liablity_id = $request->input('chart_of_account_id');
            $transaction->updated_by = auth()->user()->id;
            $transaction->save();

            // Apply new account balance
            $newAccountId = $transaction->account_id;
            if ($newAccountId) {
                $newAccount = Account::find($newAccountId);
                if ($newAccount) {
                    if ($newType === 'Received') {
                        $newAccount->increment('amount', $newAmount);
                    } elseif ($newType === 'Payment') {
                        $newAccount->decrement('amount', $newAmount);
                    }
                }
            }

            return response()->json(['status' => 200, 'message' => 'Liability updated successfully', 'id' => $transaction->id]);
        });
    }
}