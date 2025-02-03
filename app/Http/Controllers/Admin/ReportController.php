<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancePayment;
use App\Models\MotherVassel;
use App\Models\Program;
use App\Models\ProgramDetail;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function challanPostingVendorReport(Request $request)
    {
        if ($request->mv_id) {
            $data = ProgramDetail::selectRaw('
                                        vendor_id,
                                        COUNT(*) as total_records,
                                        SUM(CASE WHEN headerid IS NOT NULL THEN 1 ELSE 0 END) as challan_received,
                                        SUM(CASE WHEN headerid IS NULL THEN 1 ELSE 0 END) as challan_not_received,
                                        SUM(CASE WHEN dest_status = 0 THEN 1 ELSE 0 END) as challan_not_received_status
                                    ')
                                    ->where('mother_vassel_id', $request->mv_id)
                                    ->groupBy('vendor_id')
                                    ->get();

            $vendors = Vendor::where('status', 1)->get();
            $mvassels = MotherVassel::where('status', 1)->get();
            $mid = $request->mv_id;
            return view('admin.report.beforechallanvendor', compact('mvassels', 'vendors','data','mid'));
        } else {
            $vendors = Vendor::where('status', 1)->get();
            $mvassels = MotherVassel::where('status', 1)->get();
            
            $mid = $request->mv_id ?? null;
            return view('admin.report.beforechallanvendor', compact('mvassels', 'vendors','mid'));
        }
        
    }


    public function challanPostingReport($vid, $mid)
    {
        // $data = Program::with('programDetail','programDetail.programDestination','programDetail.programDestination.destinationSlabRate')->first();

        $data = ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('vendor_id', $vid)->where('mother_vassel_id', $mid)->whereNotNull('headerid')->get();

        $missingHeaderIds = ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('vendor_id', $vid)->where('mother_vassel_id', $mid)->whereNull('headerid')->get();
        
        $vendor = Vendor::select('id','name')->where('id',$vid)->first();
        $motherVesselName = MotherVassel::where('id', $mid)->first()->name;

        return view('admin.report.challanPostingReport', compact('data','vendor','motherVesselName','missingHeaderIds'));
    }

    public function challanPostingDateReport($id)
    {
        $data = ProgramDetail::with('programDestination','programDestination.destinationSlabRate')->where('mother_vassel_id', $id)->whereNotNull('headerid')->get();
        $data = $data->groupBy(function($item) {
            return $item->created_at->format('Y-m-d');
        });
        dd($data);

        // $mdata = DB::table('program_details')
        //                 ->select(DB::raw('DATE_FORMAT(date, "%M-%Y") as month_year'), DB::raw('SUM(riyal_amount) as total'))
        //                 ->where('status', 2)
        //                 ->groupBy('month_year')
        //                 ->orderBy('date', 'DESC')
        //                 ->get();


        $motherVesselName = MotherVassel::where('id', $id)->first()->name;
        return view('admin.report.dailyposting', compact('data','motherVesselName'));
    }

}
