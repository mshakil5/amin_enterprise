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
use App\Models\VendorSequenceNumber;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        if (!(in_array('19', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        if ($request->ajax()) {
            $transactions = Transaction::with('chartOfAccount')
                ->where('table_type', 'Income')
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
                    return $transaction->chartOfAccount ? $transaction->chartOfAccount->account_name : $transaction->description;
                })
                ->addColumn('amount_formatted', function ($transaction) {
                    $amount = $transaction->amount;
                    $class = $amount >= 0 ? 'text-success' : 'text-danger';
                    return '<span class="font-weight-bold ' . $class . '">' . number_format($amount, 2) . '</span>';
                })
                ->addColumn('tran_type_badge', function ($transaction) {
                    $type = $transaction->tran_type;
                    $configs = [
                        'Current'       => ['badge-success', 'fa-arrow-down'],
                        'Refund'        => ['badge-danger', 'fa-arrow-up'],
                        'Advance Adjust'=> ['badge-warning', 'fa-exchange-alt'],
                    ];
                    $config = $configs[$type] ?? ['badge-dark', 'fa-question'];
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
                ->addColumn('ref', function ($transaction) {
                    $tranid = $transaction->tran_id;
                    $class = 'text-success';
                    return '<span class="font-weight-bold ' . $class . '">' . $tranid . '</span>';
                })
                ->rawColumns(['chart_of_account', 'amount_formatted', 'tran_type_badge', 'payment_badge', 'ref'])
                ->make(true);
        }

        $accounts = ChartOfAccount::where('account_head', 'Income')->get();
        $accountList = Account::latest()->get();
        return view('admin.transactions.income', compact('accounts', 'accountList'));
    }

    public function getSummary(Request $request)
    {
        $baseQuery = Transaction::where('table_type', 'Income');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $baseQuery->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $totalIncome = (clone $baseQuery)->where('tran_type', 'Current')->sum('amount');
        $totalRefund = (clone $baseQuery)->where('tran_type', 'Refund')->sum('amount');
        $totalAdvanceAdjust = (clone $baseQuery)->where('tran_type', 'Advance Adjust')->sum('amount');
        $netIncome = $totalIncome - $totalRefund;
        $totalCount = (clone $baseQuery)->count();

        $todayIncome = Transaction::where('table_type', 'Income')
            ->where('tran_type', 'Current')
            ->whereDate('date', today())
            ->sum('amount');

        $monthIncome = Transaction::where('table_type', 'Income')
            ->where('tran_type', 'Current')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        return response()->json([
            'total_income' => number_format(abs($totalIncome), 2),
            'total_refund' => number_format(abs($totalRefund), 2),
            'total_advance_adjust' => number_format($totalAdvanceAdjust, 2),
            'net_income' => number_format($netIncome, 2),
            'total_count' => $totalCount,
            'today_income' => number_format($todayIncome, 2),
            'month_income' => number_format($monthIncome, 2),
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'transaction_type' => 'required|in:Current,Refund,Advance Adjust',
            'payment_type' => 'required_unless:transaction_type,Advance Adjust|nullable|in:Cash,Bank',
            'account_id' => 'required_unless:transaction_type,Advance Adjust|nullable|exists:accounts,id',
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
            $transaction->account_id = $request->transaction_type === 'Advance Adjust' ? null : ($request->input('account_id') ?? null);
            $transaction->table_type = 'Income';
            $transaction->ref = $request->input('ref');
            $transaction->description = $request->input('description');
            $transaction->amount = $request->input('amount');
            $transaction->tax_rate = $request->input('tax_rate');
            $transaction->tax_amount = $request->input('tax_amount');
            $transaction->vat_rate = $request->input('vat_rate');
            $transaction->vat_amount = $request->input('vat_amount');
            $transaction->at_amount = $request->input('at_amount');
            $transaction->tran_type = $request->input('transaction_type');
            $transaction->payment_type = $request->transaction_type === 'Advance Adjust' ? null : $request->input('payment_type');
            $transaction->income_id = $request->input('chart_of_account_id');
            $transaction->vendor_id = $request->input('vendor_id');
            $transaction->vendor_sequence_number_id = $request->input('vendor_sequence_id');
            $transaction->mother_vassel_id = $request->input('mother_vassel_id');
            $transaction->created_by = Auth()->user()->id;

            // Handle Advance Adjust specific fields
            if ($request->input('transaction_type') === 'Advance Adjust') {
                $transaction->tax_rate = null;
                $transaction->tax_amount = null;
                $transaction->vat_rate = null;
                $transaction->vat_amount = null;
                $transaction->at_amount = (float) $request->input('amount');
            }

            $transaction->save();
            $transaction->tran_id = 'IN' . date('ymd') . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
            $transaction->save();

            // Update account balance
            if ($transaction->account_id) {
                $account = Account::find($transaction->account_id);
                if ($account) {
                    $amount = (float) $request->input('amount');
                    if ($request->transaction_type === 'Current') {
                        $account->increment('amount', $amount);
                    } elseif ($request->transaction_type === 'Refund') {
                        $account->decrement('amount', $amount);
                    }
                }
            }

            DB::commit();

            return response()->json(['status' => 200, 'message' => 'Income created successfully', 'id' => $transaction->id]);

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
            'tran_id' => $transaction->tran_id,
            'date' => $transaction->date,
            'chart_of_account_id' => $transaction->chart_of_account_id,
            'chart_of_account_name' => $transaction->chartOfAccount ? $transaction->chartOfAccount->account_name : '',
            'ref' => $transaction->ref,
            'transaction_type' => $transaction->tran_type,
            'amount' => $transaction->amount,
            'tax_rate' => $transaction->tax_rate,
            'tax_amount' => $transaction->tax_amount,
            'vat_rate' => $transaction->vat_rate,
            'vat_amount' => $transaction->vat_amount,
            'at_amount' => $transaction->at_amount,
            'payment_type' => $transaction->payment_type,
            'description' => $transaction->description,
            'mother_vassel_id' => $transaction->mother_vassel_id,
            'vendor_sequence_number_id' => $transaction->vendor_sequence_number_id,
            'vendor_id' => $transaction->vendor_id,
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
            'transaction_type' => 'required|in:Current,Refund,Advance Adjust',
            'payment_type' => 'required_unless:transaction_type,Advance Adjust|nullable|in:Cash,Bank',
            'account_id' => 'required_unless:transaction_type,Advance Adjust|nullable|exists:accounts,id',
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

            $oldAccountId = $transaction->account_id;
            $oldTranType = $transaction->tran_type;
            // FIX: Cast to float to prevent non-numeric error
            $oldAmount = (float) ($transaction->amount ?? 0);

            // Reverse old account balance
            if ($oldAccountId) {
                $oldAccount = Account::find($oldAccountId);
                if ($oldAccount) {
                    if ($oldTranType === 'Current') {
                        $oldAccount->decrement('amount', $oldAmount);
                    } elseif ($oldTranType === 'Refund') {
                        $oldAccount->increment('amount', $oldAmount);
                    }
                }
            }

            // FIX: Cast new amount to float
            $newAmount = (float) ($request->input('amount') ?? 0);
            $newTranType = $request->input('transaction_type');

            $transaction->date = $request->input('date');
            $transaction->chart_of_account_id = $request->input('chart_of_account_id');
            $transaction->ref = $request->input('ref');
            $transaction->description = $request->input('description');
            $transaction->amount = $newAmount;
            $transaction->vat_rate = $request->input('vat_rate');
            $transaction->vat_amount = $request->input('vat_amount');
            $transaction->tran_type = $newTranType;
            $transaction->income_id = $request->input('chart_of_account_id');
            $transaction->mother_vassel_id = $request->input('mother_vassel_id');
            $transaction->vendor_id = $request->input('vendor_id');
            $transaction->vendor_sequence_number_id = $request->input('vendor_sequence_id');
            $transaction->updated_by = Auth()->user()->id;

            // FIX: Handle Advance Adjust properly
            if ($newTranType === 'Advance Adjust') {
                $transaction->tax_rate = null;
                $transaction->tax_amount = null;
                $transaction->vat_rate = null;
                $transaction->vat_amount = null;
                $transaction->payment_type = null;
                $transaction->account_id = null;
                $transaction->at_amount = $newAmount;
            } else {
                $transaction->tax_rate = $request->input('tax_rate');
                $transaction->tax_amount = $request->input('tax_amount');
                $transaction->payment_type = $request->input('payment_type');
                $transaction->account_id = $request->input('account_id') ?? null;
                $transaction->at_amount = $request->input('at_amount');
            }

            $transaction->save();

            // Apply new account balance
            $newAccountId = $transaction->account_id;
            if ($newAccountId && $newTranType !== 'Advance Adjust') {
                $newAccount = Account::find($newAccountId);
                if ($newAccount) {
                    if ($newTranType === 'Current') {
                        $newAccount->increment('amount', $newAmount);
                    } elseif ($newTranType === 'Refund') {
                        $newAccount->decrement('amount', $newAmount);
                    }
                }
            }

            return response()->json(['status' => 200, 'message' => 'Income updated successfully', 'id' => $transaction->id]);
        });
    }
}