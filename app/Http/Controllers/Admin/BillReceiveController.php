<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\BillReceive;

class BillReceiveController extends Controller
{
    
    public function storeBillReceive(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'date' => 'required|date',
            'bill_number' => 'required|string|max:255',
            'client_id' => 'required|integer',
            'mother_vassel_id' => 'required|integer',
            'rcv_type' => 'required|string',
            'qty' => 'nullable|string',
            'total_amount' => 'nullable|numeric',
            'maintainance' => 'nullable|numeric',
            'scale_charge' => 'nullable|numeric',
            'other_exp' => 'nullable|numeric',
            'other_rcv' => 'nullable|numeric',
            'net_amount' => 'nullable|numeric',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $billReceive = BillReceive::create([
                'date'             => $request->date,
                'bill_number'      => $request->bill_number,
                'mother_vassel_id' => $request->mother_vassel_id,
                'program_id'       => $request->program_id,
                'client_id'        => $request->client_id,
                'rcv_type'         => $request->rcv_type,
                'qty'              => $request->qty,
                'total_amount'     => $request->total_amount ?? 0.00,
                'maintainance'     => $request->maintainance ?? 0.00,
                'scale_charge'     => $request->scale_charge ?? 0.00,
                'other_exp'        => $request->other_exp ?? 0.00,
                'other_rcv'        => $request->other_rcv ?? 0.00,
                'net_amount'       => $request->net_amount ?? 0.00,
                'note'             => $request->note,
                'status'           => 1,
                'created_by'       => Auth::user()->name ?? 'System',
                'updated_by'       => Auth::user()->name ?? 'System',
            ]);

            DB::commit();

            // Log success
            Log::info('Bill Receive created successfully.', ['bill_receive_id' => $billReceive->id]);

            return response()->json([
                'success' => true,
                'message' => 'Bill received successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error
            Log::error('Error storing Bill Receive: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the bill. Please check the logs.'
            ], 500);
        }
    }

}
