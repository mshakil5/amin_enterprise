<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Vendor;
use App\Models\VendorSequenceNumber;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


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

        $vendor = Vendor::where('id',$id)->first();
        $vendorStartBalance = 0;

        
        $startDate = Carbon::parse('2025-07-20');

        $vsequence = VendorSequenceNumber::with([
                'programDetail' => function ($query) {
                    $query->select(
                        'mother_vassel_id',
                        'vendor_sequence_number_id',
                        DB::raw('COUNT(DISTINCT challan_no) as total_trip'),
                        DB::raw('SUM(carrying_bill) as total_carrying_bill'),
                        DB::raw('SUM(dest_qty) as total_qty'),
                        DB::raw('SUM(scale_fee) as total_scale_fee'),
                        DB::raw('SUM(COALESCE(advance_payments.fuelamount,0) + COALESCE(advance_payments.cashamount,0)) as total_advance')
                    )
                    ->leftJoin('advance_payments', 'program_details.id', '=', 'advance_payments.program_detail_id')
                    ->with('motherVassel:id,name')
                    ->groupBy('mother_vassel_id', 'vendor_sequence_number_id');
                },
                'programDetail.advancePayment' => function ($query) {
                    $query->select(
                        'program_detail_id',
                        'fuelqty',
                        'fuelamount',
                        'cashamount'
                    );
                },
                'transaction' => function ($query) {
                    $query->select(
                        'id',
                        'vendor_sequence_number_id',
                        'program_detail_id',
                        'date',
                        'tran_type',
                        'payment_type',
                        'at_amount',
                        'account_id',
                        'note',
                        'description'
                    );
                }
            ])
            ->where('created_at', '>', $startDate)
            ->where('vendor_id', $id)
            ->orderBy('id', 'ASC')
            ->get();

        return view('admin.accounts.ledger.vendor2', compact('data', 'totalBalance', 'vendor', 'id', 'vsequence'));
    }

    public function calculateBalance()
    {
        
        $startDate = Carbon::parse('2025-07-20');
        $vendorStartBalance = 0;

        $vsequence = VendorSequenceNumber::where('created_at', '<=', $startDate)->get();

    }
}
