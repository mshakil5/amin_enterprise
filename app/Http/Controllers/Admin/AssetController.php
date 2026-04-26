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

class AssetController extends Controller
{
    public function index(Request $request)
    {
        if (!(in_array('21', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        
        if ($request->ajax()) {
            $transactions = Transaction::with(['chartOfAccount', 'account'])
                ->where('table_type', 'Assets')
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
                    $type = $transaction->tran_type;
                    
                    if ($type === 'Payment' || $type === 'Purchase' || $type === 'Depreciation') {
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
                        'Purchase'     => ['badge-dark', 'fa-shopping-cart'],
                        'Sold'         => ['badge-success', 'fa-tag'],
                        'Depreciation' => ['badge-secondary', 'fa-chart-line'],
                        'Received'     => ['badge-primary', 'fa-arrow-down'],
                        'Payment'      => ['badge-danger', 'fa-arrow-up'],
                    ];
                    
                    $config = $configs[$type] ?? ['badge-warning', 'fa-exchange-alt'];
                    return '<span class="badge ' . $config[0] . '"><i class="fas ' . $config[1] . ' mr-1"></i>' . $type . '</span>';
                })
                ->addColumn('payment_badge', function ($transaction) {
                    if (!$transaction->payment_type) return '<span class="text-muted">-</span>';
                    
                    $type = $transaction->payment_type;
                    $configs = [
                        'Cash'              => ['badge-info', 'fa-money-bill'],
                        'Bank'              => ['badge-primary', 'fa-university'],
                        'Account Payable'   => ['badge-warning', 'fa-file-invoice-dollar'],
                        'Account Receivable'=> ['badge-success', 'fa-hand-holding-usd'],
                    ];
                    
                    $config = $configs[$type] ?? ['badge-secondary', 'fa-question'];
                    return '<span class="badge ' . $config[0] . '"><i class="fas ' . $config[1] . ' mr-1"></i>' . $type . '</span>';
                })
                ->rawColumns(['chart_of_account', 'accountname', 'amount_formatted', 'tran_type_badge', 'payment_badge'])
                ->make(true);
        }
        
        $accounts = ChartOfAccount::where('account_head', 'Assets')->get();
        $payableAccounts = ChartOfAccount::where('sub_account_head', 'Account Payable')->get(['account_name', 'id']);
        $receivableAccounts = ChartOfAccount::where('sub_account_head', 'Account Receivable')->get(['account_name', 'id']);
        $accountList = Account::latest()->get();
        
        return view('admin.transactions.assets', compact('accounts', 'accountList', 'payableAccounts', 'receivableAccounts'));
    }

    public function getSummary(Request $request)
    {
        $baseQuery = Transaction::where('table_type', 'Assets');
        
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $totalPurchase = (clone $baseQuery)->where('tran_type', 'Purchase')->sum('amount');
        $totalSold = (clone $baseQuery)->where('tran_type', 'Sold')->sum('amount');
        $totalDepreciation = (clone $baseQuery)->where('tran_type', 'Depreciation')->sum('amount');
        $totalReceived = (clone $baseQuery)->where('tran_type', 'Received')->sum('amount');
        $totalPayment = (clone $baseQuery)->where('tran_type', 'Payment')->sum('amount');
        
        $netAssetValue = $totalPurchase - $totalSold - $totalDepreciation + $totalReceived - $totalPayment;
        $totalCount = (clone $baseQuery)->count();

        return response()->json([
            'total_purchase' => number_format($totalPurchase, 2),
            'total_sold' => number_format($totalSold, 2),
            'total_depreciation' => number_format($totalDepreciation, 2),
            'net_asset_value' => number_format($netAssetValue, 2),
            'total_count' => $totalCount,
            'total_inflow' => number_format($totalSold + $totalReceived, 2),
            'total_outflow' => number_format($totalPurchase + $totalPayment + $totalDepreciation, 2),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:Purchase,Sold,Depreciation,Received,Payment',
            'payment_type' => 'required_unless:transaction_type,Depreciation|nullable|in:Cash,Bank,Account Payable,Account Receivable',
            'account_id' => 'nullable|exists:accounts,id',
        ], [
            'date.required' => 'Date field is required',
            'chart_of_account_id.required' => 'Chart of Account is required',
            'amount.required' => 'Amount field is required',
            'transaction_type.required' => 'Transaction Type is required',
            'payment_type.required_unless' => 'Payment Type is required',
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
            $transaction->created_by = Auth()->user()->id;

            $transType = $request->input('transaction_type');

            // FIX: Handle Depreciation - no payment type
            if ($transType === 'Depreciation') {
                $transaction->payment_type = null;
                $transaction->account_id = null;
            } else {
                $transaction->payment_type = $request->input('payment_type');
            }

            if ($transType === 'Purchase') {
                $transaction->liability_id = $request->input('payable_holder_id');
            }
            
            if ($transType === 'Sold') {
                $transaction->asset_id = $request->input('recivible_holder_id');
            }

            $transaction->save();
            $transaction->tran_id = 'AT' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            // FIX: Cast to float, use increment/decrement for atomicity
            $amount = (float) $request->input('amount');

            if ($transaction->account_id && $transType !== 'Depreciation') {
                $account = Account::find($transaction->account_id);
                if ($account) {
                    if ($transType === 'Received' || $transType === 'Sold') {
                        $account->increment('amount', $amount);
                    } elseif ($transType === 'Payment' || $transType === 'Purchase') {
                        $account->decrement('amount', $amount);
                    }
                }
            }

            DB::commit();

            return response()->json(['status' => 200, 'message' => 'Asset created successfully', 'id' => $transaction->id]);

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
        $chartOfAccount = ChartOfAccount::find($transaction->chart_of_account_id);
    
        $responseData = [
            'id' => $transaction->id,
            'tran_id' => $transaction->tran_id,  // FIX: Added tran_id
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
            'recivible_holder_id' => $transaction->asset_id,
        ];
        return response()->json($responseData);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:Purchase,Sold,Depreciation,Received,Payment',
            'payment_type' => 'required_unless:transaction_type,Depreciation|nullable|in:Cash,Bank,Account Payable,Account Receivable',
            'account_id' => 'nullable|exists:accounts,id',
        ], [
            'date.required' => 'Date field is required',
            'chart_of_account_id.required' => 'Chart of Account is required',
            'amount.required' => 'Amount field is required',
            'transaction_type.required' => 'Transaction Type is required',
            'payment_type.required_unless' => 'Payment Type is required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 303, 'message' => $validator->errors()->first()]);
        }

        return DB::transaction(function () use ($request, $id) {

            $transaction = Transaction::findOrFail($id);

            // FIX: Cast to float to prevent non-numeric error
            $oldAccountId = $transaction->account_id;
            $oldType = $transaction->tran_type;
            $oldAmount = (float) ($transaction->amount ?? 0);

            $newType = $request->input('transaction_type');
            $newAmount = (float) ($request->input('amount') ?? 0);

            // Reverse old account balance (skip if old type was Depreciation)
            if ($oldAccountId && $oldType !== 'Depreciation') {
                $oldAccount = Account::find($oldAccountId);
                if ($oldAccount) {
                    if ($oldType === 'Received' || $oldType === 'Sold') {
                        $oldAccount->decrement('amount', $oldAmount);
                    } elseif ($oldType === 'Payment' || $oldType === 'Purchase') {
                        $oldAccount->increment('amount', $oldAmount);
                    }
                }
            }

            // Update transaction fields
            $transaction->date = $request->input('date');
            $transaction->chart_of_account_id = $request->input('chart_of_account_id');
            $transaction->ref = $request->input('ref');
            $transaction->description = $request->input('description');
            $transaction->amount = $newAmount;
            $transaction->tax_rate = $request->input('tax_rate');
            $transaction->tax_amount = $request->input('tax_amount');
            $transaction->vat_rate = $request->input('vat_rate');
            $transaction->vat_amount = $request->input('vat_amount');
            $transaction->at_amount = $request->input('at_amount');
            $transaction->tran_type = $newType;
            $transaction->updated_by = Auth()->user()->id;

            // FIX: Clear liability/asset IDs first
            $transaction->liability_id = null;
            $transaction->asset_id = null;

            // FIX: Handle Depreciation - clear payment and account
            if ($newType === 'Depreciation') {
                $transaction->payment_type = null;
                $transaction->account_id = null;
            } else {
                $transaction->payment_type = $request->input('payment_type');
                $transaction->account_id = $request->input('account_id') ?? null;
            }

            if ($newType === 'Purchase') {
                $transaction->liability_id = $request->input('payable_holder_id');
            }

            if ($newType === 'Sold') {
                $transaction->asset_id = $request->input('recivible_holder_id');
            }

            $transaction->save();

            // Apply new account balance (skip if new type is Depreciation)
            $newAccountId = $transaction->account_id;
            if ($newAccountId && $newType !== 'Depreciation') {
                $newAccount = Account::find($newAccountId);
                if ($newAccount) {
                    if ($newType === 'Received' || $newType === 'Sold') {
                        $newAccount->increment('amount', $newAmount);
                    } elseif ($newType === 'Payment' || $newType === 'Purchase') {
                        $newAccount->decrement('amount', $newAmount);
                    }
                }
            }

            return response()->json(['status' => 200, 'message' => 'Asset updated successfully', 'id' => $transaction->id]);
        });
    }
}