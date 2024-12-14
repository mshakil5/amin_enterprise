<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DaybookController extends Controller
{
    public function cashbook(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $vendor_id = $request->input('vendor_id');
        $mv_id = $request->input('mv_id');

        $cashbooks = Transaction::where('payment_type', 'Cash')
        ->whereIn('tran_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->when($vendor_id, function($query, $vendor_id) {
            return $query->where('vendor_id', '=', $vendor_id);
        })
        ->when($mv_id, function($query, $mv_id) {
            return $query->where('mother_vassel_id', '=', $mv_id);
        })
        ->orderBy('id', 'desc')
        ->get();
        
        $totalDrAmount = Transaction::where('payment_type', 'Cash')
        ->whereIn('tran_type', ['Current', 'Received', 'Sold', ])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->when($vendor_id, function($query, $vendor_id) {
            return $query->where('vendor_id', '=', $vendor_id);
        })
        ->when($mv_id, function($query, $mv_id) {
            return $query->where('mother_vassel_id', '=', $mv_id);
        })
        ->sum('amount');

        $totalCrAmount = Transaction::where('payment_type', 'Cash')
        ->whereIn('tran_type', ['Purchase', 'Payment', 'Advance'])
        ->when($startDate, function($query, $startDate) {
            return $query->whereDate('date', '>=', $startDate);
        })
        ->when($endDate, function($query, $endDate) {
            return $query->whereDate('date', '<=', $endDate);
        })
        ->when($vendor_id, function($query, $vendor_id) {
            return $query->where('vendor_id', '=', $vendor_id);
        })
        ->when($mv_id, function($query, $mv_id) {
            return $query->where('mother_vassel_id', '=', $mv_id);
        })
        ->sum('amount');

        $totalAmount = $totalDrAmount - $totalCrAmount;
        return view('admin.accounts.daybook.cashbook', compact('cashbooks', 'totalAmount'));
    }

    public function bankbook(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $bankbooks = Transaction::where('payment_type', 'Bank')
            ->whereIn('tran_type', ['Current', 'Received', 'Sold', 'Advance', 'Purchase', 'Payment', 'Prepaid'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->orderBy('id', 'desc')
            ->get();

        $totalDrAmount = Transaction::where('payment_type', 'Bank')
            ->whereIn('tran_type', ['Current', 'Received', 'Sold', 'Advance'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('amount');

        $totalCrAmount = Transaction::where('payment_type', 'Bank')
            ->whereIn('tran_type', ['Purchase', 'Payment', 'Prepaid'])
            ->when($startDate, function($query, $startDate) {
                return $query->whereDate('date', '>=', $startDate);
            })
            ->when($endDate, function($query, $endDate) {
                return $query->whereDate('date', '<=', $endDate);
            })
            ->sum('amount');

        $totalAmount = $totalDrAmount - $totalCrAmount;

        return view('admin.accounts.daybook.bankbook', compact('bankbooks', 'totalAmount'));
    }
}