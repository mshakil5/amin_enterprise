<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use App\Models\Vendor;
use App\Models\VendorSequenceNumber;
use Carbon\Carbon;
use App\Services\TrialBalanceService;

class TrialBalanceController extends Controller
{
    protected $trialBalanceService;

    // Inject the service class
    public function __construct(TrialBalanceService $trialBalanceService)
    {
        $this->trialBalanceService = $trialBalanceService;
    }

    public function trialBalance(Request $request)
    {
        $startDate = '2025-07-20';
        $endDate = $request->filled('end_date') ? $request->end_date : now()->toDateString();

        // Get all processed data from the service
        $data = $this->trialBalanceService->generateTrialBalanceData($startDate, $endDate);

        // Add dates to the array for the view
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        return view('admin.accounts.trial_balance.index', $data);
    }
    
}