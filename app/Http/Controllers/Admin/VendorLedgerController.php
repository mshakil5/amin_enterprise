<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorLedgerController extends Controller
{
    public function vendor($id, Request $request)
    {
        $data = collect();
        $totalDrAmount = $totalCrAmount = $totalBalance = 0;

        if ($request->start_date) {
            $data = Transaction::where('vendor_id', $id)
                ->whereDate('date', '>=', '2025-06-28')
                ->whereDate('date', '>=', $request->start_date)
                ->when($request->end_date, function ($query) use ($request) {
                    $query->whereDate('date', '<=', $request->end_date);
                })
                ->orderBy('id', 'DESC')
                ->get();

            $totalDrAmount = $data->where('tran_type', 'Wallet')->sum('amount');
            $totalCrAmount = $data->whereIn('payment_type', ['Cash', 'Fuel', 'Wallet'])->sum('amount');
            $totalBalance = $totalCrAmount - $totalDrAmount;
        }

        $accountName = Vendor::find($id)->name ?? 'N/A';

        return view('admin.accounts.ledger.vendor2', compact('data', 'totalBalance', 'accountName', 'id'));
    }
}
