<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MotherVassel;
use App\Models\Program;
use App\Models\ProgramDetail;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function challanPostingVendorReport(Request $request)
    {
        if ($request->mv_id) {
            $data = ProgramDetail::selectRaw('
                                        vendor_id,
                                        COUNT(*) as total_records,
                                        SUM(CASE WHEN dest_status = 1 THEN 1 ELSE 0 END) as challan_received,
                                        SUM(CASE WHEN dest_status = 0 THEN 1 ELSE 0 END) as challan_not_received
                                    ')
                                    ->where('mother_vassel_id', $request->mv_id)
                                    ->groupBy('vendor_id')
                                    ->get();

            $vendors = Vendor::where('status', 1)->get();
            $mvassels = MotherVassel::where('status', 1)->get();
            return view('admin.report.beforechallanvendor', compact('mvassels', 'vendors','data'));
        } else {
            $vendors = Vendor::where('status', 1)->get();
            $mvassels = MotherVassel::where('status', 1)->get();
            return view('admin.report.beforechallanvendor', compact('mvassels', 'vendors'));
        }
        
    }

}
