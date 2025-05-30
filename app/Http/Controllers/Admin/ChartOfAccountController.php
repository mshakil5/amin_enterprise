<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Models\ChartOfAccount;
use Illuminate\Support\Carbon;
use App\Models\Branch;


class ChartOfAccountController extends Controller
{
    public function index(Request $request)
    {
        if (!(in_array('18', json_decode(auth()->user()->role->permission)))) {
          return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }
        if ($request->ajax()) {
            $query = ChartOfAccount::where('status', 1);

            if ($accountHead = $request->input('account_head')) {
                $query->where('account_head', $accountHead);
            }

            if ($subAccountHead = $request->input('sub_account_head')) {
                $query->where('sub_account_head', $subAccountHead);
            }

            $chartOfAccounts = $query->orderBy('id', 'desc')->get();

            return DataTables::of($chartOfAccounts)
                ->make(true);
        }

        $accountHeads = ChartOfAccount::distinct()->pluck('account_head');
        $subAccountHeads = ChartOfAccount::distinct()->pluck('sub_account_head');

        return view('admin.chart_of_accounts.index', compact('accountHeads', 'subAccountHeads'));
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

        $existingAccount = ChartOfAccount::where('account_name', $request->account_name)
                                     ->first();
    
        if ($existingAccount) {
            return response()->json(['status' => 303, 'message' => 'Account Name already exists for this branch..!']);
        }

        $existingSerial = ChartOfAccount::where('account_head', $request->account_head)
                                    // ->where('sub_account_head', $request->sub_account_head)
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
        $chartOfAccount->status = 1;
        $chartOfAccount->created_by = Auth::user()->id;
        $chartOfAccount->save();

        return response()->json(['status' => 200, 'message' => 'Created Successfully']);
    }

    public function edit($id)
    {
        $chartDtl = ChartOfAccount::where('id', '=', $id)->first();
        if(empty($chartDtl)){
            return response()->json(['status'=> 303,'message'=>"No data found"]);
        }else{
            return response()->json(['status'=> 300,
            'account_head'=>$chartDtl->account_head,
            'sub_account_head'=>$chartDtl->sub_account_head,
            'id'=>$chartDtl->id,
            'account_name'=>$chartDtl->account_name,
            'description'=>$chartDtl->description,
            'contingent'=>$chartDtl->contingent,
            'serial'=>$chartDtl->serial]);
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
            // ->where('sub_account_head', $request->sub_account_head)
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
            $chartOfAccount->status=1;
        }
        $chartOfAccount->save();
        return $chartOfAccount;
    }
}
