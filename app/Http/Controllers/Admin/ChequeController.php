<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\ChequeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ChequeController extends Controller
{
    /**
     * Check if cheque exists for selected bills
     */
    public function checkExisting(Request $request)
    {
        $billNos = $request->bill_nos;

        $cheque = ChequeDetail::where(function($query) use ($billNos) {
            foreach ($billNos as $billNo) {
                $query->orWhere('bill_nos', 'LIKE', "%\"$billNo\"%")
                    ->orWhere('bill_nos', 'LIKE', "%$billNo%");
            }
        })->latest()->first();

        if ($cheque) {
            return response()->json([
                'exists' => true,
                'cheque' => [
                    'id'             => $cheque->id,
                    'cheque_number'  => $cheque->cheque_number,
                    'cheque_date'    => $cheque->cheque_date,
                    'bank_name'      => $cheque->bank_name,
                    'cheque_amount'  => $cheque->cheque_amount,
                    // Use asset() for public folder files
                    'document_path'  => $cheque->document_path ? asset($cheque->document_path) : null,
                    'document_name'  => $cheque->document_name,
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
 * Store cheque details
 */
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'bill_receive_id' => 'required|exists:bill_receives,id',
        'cheque_number'   => 'required|string|max:50',
        'cheque_date'     => 'required|date',
        'bank_name'       => 'nullable|string|max:100',
        'cheque_amount'   => 'required|numeric|min:0',
        'bill_nos'        => 'required|string',
        'cheque_document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $documentPath = null;
    $documentName = null;

    if ($request->hasFile('cheque_document')) {
        $file         = $request->file('cheque_document');
        $documentName = $file->getClientOriginalName();

        // Store directly in public/images/documents/
        $destinationPath = public_path('images/documents');

        // Create folder if it doesn't exist
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Generate unique filename to avoid conflicts
        $fileName      = time() . '_' . str_replace(' ', '_', $documentName);
        $file->move($destinationPath, $fileName);

        // Save relative path for retrieval
        $documentPath  = 'images/documents/' . $fileName;
    }

    $billNosArray = explode(',', $request->bill_nos);

    ChequeDetail::create([
        'bill_receive_id' => $request->bill_receive_id,
        'bill_nos'        => json_encode($billNosArray),
        'cheque_number'   => $request->cheque_number,
        'cheque_date'     => $request->cheque_date,
        'bank_name'       => $request->bank_name,
        'cheque_amount'   => $request->cheque_amount,
        'document_path'   => $documentPath,
        'document_name'   => $documentName,
        'created_by'      => auth()->id(),
    ]);

    return response()->json([
        'message' => 'Cheque details saved successfully!',
    ]);
}

/**
 * Update cheque details
 */
public function update(Request $request, $id)
{
    $cheque = ChequeDetail::findOrFail($id);

    $validator = Validator::make($request->all(), [
        'cheque_number'   => 'required|string|max:50',
        'cheque_date'     => 'required|date',
        'bank_name'       => 'nullable|string|max:100',
        'cheque_amount'   => 'required|numeric|min:0',
        'bill_nos'        => 'required|string',
        'cheque_document' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png,doc,docx',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Handle new document upload
    if ($request->hasFile('cheque_document')) {
        // Delete old document from public/images/documents/
        if ($cheque->document_path) {
            $oldFilePath = public_path($cheque->document_path);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        $file         = $request->file('cheque_document');
        $documentName = $file->getClientOriginalName();

        $destinationPath = public_path('images/documents');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $fileName      = time() . '_' . str_replace(' ', '_', $documentName);
        $file->move($destinationPath, $fileName);

        $cheque->document_path = 'images/documents/' . $fileName;
        $cheque->document_name = $documentName;
    }

    $billNosArray = explode(',', $request->bill_nos);

    $cheque->update([
        'bill_nos'      => json_encode($billNosArray),
        'cheque_number' => $request->cheque_number,
        'cheque_date'   => $request->cheque_date,
        'bank_name'     => $request->bank_name,
        'cheque_amount' => $request->cheque_amount,
        'updated_by'    => auth()->id(),
    ]);

    return response()->json([
        'message' => 'Cheque details updated successfully!',
    ]);
}


/**
 * View cheque details (for modal)
 */
public function view(Request $request)
{
    $cheque = ChequeDetail::findOrFail($request->cheque_id);

    return response()->json([
        'cheque' => [
            'id'             => $cheque->id,
            'cheque_number'  => $cheque->cheque_number,
            'cheque_date'    => $cheque->cheque_date,
            'bank_name'      => $cheque->bank_name,
            'cheque_amount'  => $cheque->cheque_amount,
            'bill_nos'       => json_decode($cheque->bill_nos, true) ?? [],
            'document_path'  => $cheque->document_path ? asset($cheque->document_path) : null,
            'document_name'  => $cheque->document_name,
            'created_at'     => $cheque->created_at->format('d-m-Y h:i A'),
        ]
    ]);
}

/**
 * Delete cheque details
 */
public function delete(Request $request)
{
    $cheque = ChequeDetail::findOrFail($request->cheque_id);

    // Delete document file
    if ($cheque->document_path) {
        $filePath = public_path($cheque->document_path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    $cheque->delete();

    return response()->json([
        'message' => 'Cheque entry deleted successfully!',
    ]);
}


}