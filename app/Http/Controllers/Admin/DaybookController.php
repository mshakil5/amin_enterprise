<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DaybookController extends Controller
{
    public function cashbook(Request $request)
    {
        if (!(in_array('24', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        if ($request->ajax()) {
            $query = Transaction::where('payment_type', 'Cash')
                ->whereIn('tran_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
                ->when($request->start_date, fn($q) => $q->whereDate('date', '>=', $request->start_date))
                ->when($request->end_date, fn($q) => $q->whereDate('date', '<=', $request->end_date))
                ->when($request->vendor_id, fn($q) => $q->where('vendor_id', $request->vendor_id))
                ->when($request->mv_id, fn($q) => $q->where('mother_vassel_id', $request->mv_id))
                ->orderBy('id', 'desc')
                ->get();

            $totalDr = $query->whereIn('tran_type', ['Current', 'Received', 'Sold'])->sum('amount');
            $totalCr = $query->whereIn('tran_type', ['Purchase', 'Payment', 'Advance'])->sum('amount');
            $balance = $totalDr - $totalCr;

            $data = [];
            foreach ($query as $index => $row) {
                $debit = '';
                $credit = '';
              $current_balance = '';
                if (in_array($row->tran_type, ['Received'])) {
                    $debit = number_format($row->amount, 2);
                    $current_balance = number_format($balance, 2);
                    $balance -= $row->amount;
                } elseif (in_array($row->tran_type, ['Advance', 'Payment', 'Prepaid', 'Current'])) {
                    $credit = number_format($row->amount, 2);
                    $current_balance = number_format($balance, 2);
                    $balance += $row->amount;
                }

                $data[] = [
                    'DT_RowIndex' => $index + 1,
                    'date' => \Carbon\Carbon::parse($row->date)->format('d-m-Y'),
                    'description' => $row->description,
                    'type_label' => $row->tran_type . ' ' . $row->payment_type,
                    'voucher' => '<a href="' . route('admin.expense.voucher', $row->id) . '" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-info-circle"></i> Voucher</a>',
                    'bill_number' => $row->bill_number,
                    'challan_no' => $row->challan_no,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $current_balance
                ];
            }

            // return response()->json([
            //     'data' => $data,
            //     'final_balance' => number_format($balance, 2)
            // ]);


            return DataTables::of($data)->rawColumns(['voucher'])->make(true);
        }

        return view('admin.accounts.daybook.cashbook');
    }

    public function bankbook(Request $request)
    {
        if (!(in_array('24', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        if ($request->ajax()) {
            $query = Transaction::where('payment_type', 'Bank')
                ->whereIn('tran_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
                ->when($request->start_date, fn($q) => $q->whereDate('date', '>=', $request->start_date))
                ->when($request->end_date, fn($q) => $q->whereDate('date', '<=', $request->end_date))
                ->when($request->vendor_id, fn($q) => $q->where('vendor_id', $request->vendor_id))
                ->when($request->mv_id, fn($q) => $q->where('mother_vassel_id', $request->mv_id))
                ->orderBy('id', 'desc')
                ->get();

            $totalDr = $query->whereIn('tran_type', ['Current', 'Received', 'Sold'])->sum('amount');
            $totalCr = $query->whereIn('tran_type', ['Purchase', 'Payment', 'Advance'])->sum('amount');
            $balance = $totalDr - $totalCr;

            $data = [];
            foreach ($query as $index => $row) {
                $debit = '';
                $credit = '';
              $current_balance = '';
                if (in_array($row->tran_type, ['Received'])) {
                    $debit = number_format($row->amount, 2);
                    $current_balance = number_format($balance, 2);
                    $balance -= $row->amount;
                } elseif (in_array($row->tran_type, ['Advance', 'Payment', 'Prepaid', 'Current'])) {
                    $credit = number_format($row->amount, 2);
                    $current_balance = number_format($balance, 2);
                    $balance += $row->amount;
                }

                $data[] = [
                    'DT_RowIndex' => $index + 1,
                    'date' => \Carbon\Carbon::parse($row->date)->format('d-m-Y'),
                    'description' => $row->description,
                    'type_label' => $row->tran_type . ' ' . $row->payment_type,
                    'voucher' => '<a href="' . route('admin.expense.voucher', $row->id) . '" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-info-circle"></i> Voucher</a>',
                    'bill_number' => $row->bill_number,
                    'challan_no' => $row->challan_no,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $current_balance
                ];
            }

            return DataTables::of($data)->rawColumns(['voucher'])->make(true);
        }

        return view('admin.accounts.daybook.bankbook');
    }

public function daybook(Request $request)
{
    if ($request->ajax()) {
        $start = $request->start_date ?? date('Y-m-d');
        $end = $request->end_date ?? date('Y-m-d');

        $transactions = Transaction::whereIn('table_type', ['Income', 'Expenses', 'Assets', 'Liabilities', 'Equity'])
            ->whereDate('date', '>=', $start)
            ->whereDate('date', '<=', $end)
            ->orderBy('id', 'asc')
            ->get();

        // Calculate category totals
        $categoryTotals = [
            'Income' => Transaction::where('table_type', 'Income')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount'),
            'Expenses' => Transaction::whereIn('table_type', ['Expenses', 'Cogs'])
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount'),
            'Assets' => Transaction::where('table_type', 'Assets')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount'),
            'Liabilities' => Transaction::where('table_type', 'Liabilities')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount'),
            'Equity' => Transaction::where('table_type', 'Equity')
                ->whereDate('date', '>=', $start)
                ->whereDate('date', '<=', $end)
                ->sum('amount'),
        ];

        $data = [];
        foreach ($transactions as $index => $row) {
            $debit = '';
            $credit = '';

            if (in_array($row->tran_type, ['Received'])) {
                $debit = number_format($row->amount, 2);
            } elseif (in_array($row->tran_type, ['Advance', 'Payment', 'Prepaid', 'Current'])) {
                $credit = number_format($row->amount, 2);
            }

            $data[] = [
                'DT_RowIndex' => $index + 1,
                'date' => \Carbon\Carbon::parse($row->date)->format('d-m-Y'),
                'description' => $row->description,
                'type_label' => $row->tran_type . ' ' . $row->payment_type,
                'voucher' => '<a href="' . route('admin.expense.voucher', $row->id) . '" target="_blank" class="btn btn-info btn-xs"><i class="fa fa-info-circle"></i> Voucher</a>',
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        return DataTables::of($data)
            ->with([
                'categoryTotals' => $categoryTotals
            ])
            ->rawColumns(['voucher'])
            ->make(true);
    }

    return view('admin.accounts.daybook.index');
}


}
