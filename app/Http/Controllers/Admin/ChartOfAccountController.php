<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\ChartOfAccount;
use App\Models\PetrolPump;
use Illuminate\Support\Carbon;
use App\Models\Transaction;

class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        if (!(in_array('18', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        if ($request->ajax()) {
            $query = ChartOfAccount::query();

            if ($request->filled('account_head')) {
                $query->where('account_head', $request->input('account_head'));
            }

            if ($request->filled('sub_account_head')) {
                $query->where('sub_account_head', $request->input('sub_account_head'));
            }

            $chartOfAccounts = $query->latest()->get();

            return DataTables::of($chartOfAccounts)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<label class="switch"><input type="checkbox" class="status-btn" value="'.$row->id.'" '.$checked.'><span class="slider round"></span></label>';
                })
                ->addColumn('action', function ($row) {
                    return '<button type="button" class="btn btn-warning btn-action edit-btn" data-id="'.$row->id.'" title="Edit"><i class="fas fa-edit"></i> Edit</button>';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $accountHeads = ChartOfAccount::distinct()->pluck('account_head');
        $subAccountHeads = ChartOfAccount::distinct()->pluck('sub_account_head');
        $petrolPumps = PetrolPump::all();

        return view('admin.chart_of_accounts.index', compact('accountHeads', 'subAccountHeads', 'petrolPumps'));
    }

    public function getSummary()
    {
        $totalAccounts = ChartOfAccount::count();
        $activeAccounts = ChartOfAccount::where('status', 1)->count();
        $inactiveAccounts = ChartOfAccount::where('status', 0)->count();
        $totalHeads = ChartOfAccount::distinct()->count('account_head');

        return response()->json([
            'total_accounts' => $totalAccounts,
            'active_accounts' => $activeAccounts,
            'inactive_accounts' => $inactiveAccounts,
            'total_heads' => $totalHeads,
        ]);
    }

    public function store(Request $request)
    {
        if (empty($request->account_name)) {
            return response()->json(['status' => 303, 'message' => 'Name Field Is Required..!']);
        }
        if (empty($request->account_head)) {
            return response()->json(['status' => 303, 'message' => 'Account Head Field Is Required..!']);
        }
        if (empty($request->sub_account_head)) {
            return response()->json(['status' => 303, 'message' => 'Sub Account Field Is Required..!']);
        }
        if (empty($request->contingent)) {
            return response()->json(['status' => 303, 'message' => 'Contigent Field Is Required..!']);
        }
        if (empty($request->serial)) {
            return response()->json(['status' => 303, 'message' => 'Account code Field Is Required..!']);
        }

        $existingAccount = ChartOfAccount::where('account_name', $request->account_name)->first();
        if ($existingAccount) {
            return response()->json(['status' => 303, 'message' => 'Account Name already exists for this branch..!']);
        }

        $existingSerial = ChartOfAccount::where('account_head', $request->account_head)
                                    ->where('serial', $request->serial)
                                    ->first();
        if ($existingSerial) {
            return response()->json(['status' => 303, 'message' => 'This account code already exists..!']);
        }

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccount->account_head = $request->account_head;
        $chartOfAccount->sub_account_head = $request->sub_account_head;
        $chartOfAccount->date = Carbon::now()->format('d-m-Y');
        $chartOfAccount->account_name = $request->account_name;
        $chartOfAccount->contingent = $request->contingent;
        $chartOfAccount->serial = $request->serial;
        $chartOfAccount->description = $request->description;
        $chartOfAccount->petrol_pumps_id = ($request->account_head == 'Liabilities') ? $request->petrol_pumps_id : null;
        $chartOfAccount->status = 1;
        $chartOfAccount->created_by = Auth::user()->id;
        $chartOfAccount->save();

        return response()->json(['status' => 200, 'message' => 'Created Successfully']);
    }

    public function edit($id)
    {
        $chartDtl = ChartOfAccount::find($id);
        if(empty($chartDtl)){
            return response()->json(['status'=> 303,'message'=>"No data found"]);
        }else{
            return response()->json([
                'status'=> 300,
                'account_head'=>$chartDtl->account_head,
                'sub_account_head'=>$chartDtl->sub_account_head,
                'id'=>$chartDtl->id,
                'account_name'=>$chartDtl->account_name,
                'description'=>$chartDtl->description,
                'contingent'=>$chartDtl->contingent,
                'serial'=>$chartDtl->serial,
                'petrol_pumps_id'=>$chartDtl->petrol_pumps_id
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        if (empty($request->account_name)) {
            return response()->json(['status' => 303, 'message' => 'Name Field Is Required..!']);
        }
        if (empty($request->account_head)) {
            return response()->json(['status' => 303, 'message' => 'Account Head Field Is Required..!']);
        }
        if (empty($request->sub_account_head)) {
            return response()->json(['status' => 303, 'message' => 'Sub Account Field Is Required..!']);
        }
        if (empty($request->contingent)) {
            return response()->json(['status' => 303, 'message' => 'Contigent Field Is Required..!']);
        }
        if (empty($request->serial)) {
            return response()->json(['status' => 303, 'message' => 'Account code Field Is Required..!']);
        }

        $chartOfAccount = ChartOfAccount::find($id);

        $existingAccount = ChartOfAccount::where('account_name', $request->account_name)
                                  ->where('id', '!=', $chartOfAccount->id)
                                  ->first();
        if ($existingAccount) {
            return response()->json(['status' => 303, 'message' => 'Account Name already exists for this branch..!']);
        }

        $existingAccount = ChartOfAccount::where('account_head', $request->account_head)
            ->where('serial', $request->serial)
            ->where('id', '!=', $id)
            ->first();
        if ($existingAccount) {
            return response()->json(['status' => 303, 'message' => 'This account code already exists..!']);
        }

        $chartOfAccount->account_head = $request->account_head;
        $chartOfAccount->sub_account_head = $request->sub_account_head;
        $chartOfAccount->account_name = $request->account_name;
        $chartOfAccount->contingent = $request->contingent;
        $chartOfAccount->serial = $request->serial;
        $chartOfAccount->description = $request->description;
        $chartOfAccount->petrol_pumps_id = ($request->account_head == 'Liabilities') ? $request->petrol_pumps_id : null;
        $chartOfAccount->updated_by = Auth::user()->id;
        $chartOfAccount->save();

        return response()->json(['status' => 200, 'message' => 'Updated Successfully']);
    }

    public function changeStatus($id)
    {
        $chartOfAccount = ChartOfAccount::find($id);
        if($chartOfAccount->status){
            $chartOfAccount->status = 0;
        }else{
            $chartOfAccount->status = 1;
        }
        $chartOfAccount->save();
        return response()->json(['status' => 200, 'message' => 'Status Updated Successfully']);
    }

    public function reverse($id)
    {
        $transaction = Transaction::with('reverseTransaction')->findOrFail($id);
        $reverse = $transaction->reverseTransaction;
        return view('admin.transactions.reverse', compact('transaction', 'reverse'));
    }

    public function reverseSave(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'date' => 'required|date',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);
        $accountHead = $transaction->table_type ?? null;

        if ($accountHead == 'Income' || $accountHead == 'Equity') {
            $parentReverseType = 'Increment';
            $childReverseType = 'Decrement';
        } else if ($accountHead == 'Assets' || $accountHead == 'Expense' || $accountHead == 'Cogs' || $accountHead == 'Liabilities') {
            $parentReverseType = 'Decrement';
            $childReverseType = 'Increment';
        } else {
            $parentReverseType = null;
            $childReverseType = null;
        }

        if ($transaction->reverseTransaction) {
            $reverse = $transaction->reverseTransaction;
            $reverse->date = $request->date;
            $reverse->note = $request->note ?? $reverse->note;
            $reverse->save();
        } else {
            $reverse = new Transaction();
            foreach ($transaction->getAttributes() as $key => $value) {
                if (!in_array($key, ['id', 'date', 'tran_id', 'note', 'reverse_id', 'reverse_type', 'created_at', 'updated_at'])) {
                    $reverse->$key = $value;
                }
            }
            $reverse->date = $request->date;
            $reverse->note = $request->note;
            $reverse->reverse_id = $transaction->id;
            $reverse->reverse_type = $childReverseType;
            $reverse->save();
            $reverse->tran_id = 'REV' . date('ymd') . str_pad($reverse->id, 4, '0', STR_PAD_LEFT);
            $reverse->save();   
            $transaction->reverse_id = $reverse->id;
            $transaction->reverse_type = $parentReverseType;
            $transaction->save();
        }

        return redirect()->back()->with('success', 'Reverse transaction saved');
    }
}