<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Str;

class VendorDocumentController extends Controller
{
    public function index($id)
    {
        $programId = $id;
        // Fetch documents related to this program
        $documents = ProgramDocument::where('program_id', $programId)->latest()->get();
        
        return view('admin.program.document', compact('programId', 'documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_id' => 'required',
            'document' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls|max:2048',
            'date' => 'required|date',
        ]);

        $data = $request->except('_token', 'document');
        $data['created_by'] = Auth::id();

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = Str::random(15) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/program_documents'), $filename);
            $data['document'] = 'uploads/program_documents/' . $filename;
        }

        ProgramDocument::create($data);

        return redirect()->route('admin.programVendorDocuments', $request->program_id)->with('success', 'Document added successfully.');
    }

    public function edit($id)
    {
        $document = ProgramDocument::findOrFail($id);
        return response()->json($document);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:program_documents,id',
            'vendor_id' => 'required',
            'date' => 'required|date',
        ]);

        $document = ProgramDocument::findOrFail($request->id);
        $data = $request->except('_token', '_method', 'document', 'id');
        $data['updated_by'] = Auth::id();

        if ($request->hasFile('document')) {
            // Delete old file
            if (file_exists(public_path($document->document))) {
                unlink(public_path($document->document));
            }
            $file = $request->file('document');
            $filename = Str::random(15) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/program_documents'), $filename);
            $data['document'] = 'uploads/program_documents/' . $filename;
        }

        $document->update($data);

        return redirect()->route('admin.programVendorDocuments', $request->program_id)->with('success', 'Document updated successfully.');
    }

    public function delete($id)
    {
        $document = ProgramDocument::findOrFail($id);
        $document->deleted_by = Auth::id();
        $document->save();
        $document->delete(); // Soft delete triggered

        return redirect()->back()->with('success', 'Document deleted successfully.');
    }
}