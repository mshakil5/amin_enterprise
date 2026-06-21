<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\DestinationSlabRate;
use App\Models\ProgramDetail;
use Illuminate\Http\Request;

class AfterChallanPostingController extends Controller
{



    public function search()
    {
        if (!(in_array('14', json_decode(auth()->user()->role->permission)))) {
            return redirect()->back()->with('error', 'Sorry, You do not have permission to access that page.');
        }

        $clients = Client::where('status', 1)->get();
        
        // Retrieve cached search filters from session
        $filters = session()->get('challan_search_filters', []);

        return view('admin.program.program_details_search', compact('clients', 'filters'));
    }

    public function checkChallanBydate(Request $request)
    {
        $fromdate = $request->input('fromdate');
        $todate   = $request->input('todate');
        $clientId = $request->input('client_id');

        // Cache the search filters in session so they persist after reload
        session()->put('challan_search_filters', [
            'fromdate' => $fromdate,
            'todate' => $todate,
            'client_id' => $clientId
        ]);

        $programDetails = ProgramDetail::with(['challanRate', 'vendor', 'program', 'ghat', 'destination', 'motherVassel'])
            ->whereBetween('date', [$fromdate, $todate])
            ->whereNotNull('headerid')
            ->where('client_id', $clientId)
            ->orderBy('id', 'ASC')
            ->get();

        if ($programDetails->isEmpty()) {
            return response()->json([
                'status'  => 400,
                'message' => '<div class="alert alert-warning">No data found for this date.</div>'
            ]);
        }

        $html = $this->generateChallanRateTable($programDetails, $clientId);

        return response()->json([
            'status' => 200,
            'html'   => $html,
            'programDetails' => $programDetails,
            'count'  => $programDetails->count()
        ]);
    }

    private function calculateNewChallanDetails($detail, $clientId)
    {
        $cQty = $detail->challanRate->sum('qty');
        if ($cQty <= 0) return ['rate' => '-', 'amount' => 0, 'diff' => 0];

        $dstnID = $detail->destination_id;
        $ghatID = $detail->ghat_id;
        
        $clientId = $clientId ?? ($detail->program->client_id ?? 3);

        $rates = DestinationSlabRate::where('destination_id', $dstnID)
                                ->where('ghat_id', $ghatID)
                                ->where('client_id', $clientId)
                                ->orderBy('tier_min_qty', 'asc')
                                ->get();

        if ($rates->isEmpty()) {
            return ['rate' => 'N/A', 'amount' => 0, 'diff' => 0];
        }

        $matchedTier = null;
        foreach ($rates as $rate) {
            $tierRate = $rate->tier_rate ?? null;
            if (!is_null($tierRate) && $tierRate > 0) {
                $minOk = ($cQty >= ($rate->tier_min_qty ?? 0));
                $maxOk = is_null($rate->tier_max_qty) || ($cQty <= $rate->tier_max_qty);

                if ($minOk && $maxOk) {
                    $matchedTier = $rate;
                    break;
                }
            }
        }

        $oldTotalAmount = $detail->challanRate->sum('total');
        $newRate = 0;
        $newAmount = 0;

        if ($matchedTier) {
            $newRate = $matchedTier->tier_rate;
            $newAmount = $cQty * $newRate;
        } else {
            $oldRate = $rates->first();
            if ($oldRate && isset($oldRate->maxqty) && $oldRate->maxqty > 0) {
                if ($cQty > $oldRate->maxqty) {
                    $aboveqty = $cQty - $oldRate->maxqty;
                    $newAmount = ($oldRate->below_rate_per_qty * $oldRate->maxqty) + ($oldRate->above_rate_per_qty * $aboveqty);
                    $newRate = "{$oldRate->below_rate_per_qty} / {$oldRate->above_rate_per_qty}";
                } else {
                    $newRate = $oldRate->below_rate_per_qty;
                    $newAmount = $oldRate->below_rate_per_qty * $cQty;
                }
            }
        }

        $diffAmount = $newAmount - $oldTotalAmount;

        return [
            'rate' => $newRate > 0 ? $newRate : '-',
            'amount' => $newAmount,
            'diff' => $diffAmount
        ];
    }

    private function generateChallanRateTable($programDetails, $clientId)
    {
        $html = '<div class="d-flex justify-content-between align-items-center mb-3">';
        $html .= '<div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAllTop">
                    <label class="form-check-label" for="selectAllTop">Select All</label>
                </div>';
        $html .= '<div>
                    <button type="button" class="btn btn-warning btn-sm" id="previewChangesBtn">
                        <i class="fas fa-eye"></i> Preview
                    </button>
                    <button type="button" class="btn btn-success btn-sm ml-2" id="confirmBulkUpdate">
                        <i class="fas fa-check"></i> Submit
                    </button>
                </div>';
        $html .= '</div>';

        $html .= '<div class="table-responsive">';
        $html .= '<table id="challanRateTable" class="table table-bordered table-striped table-hover datatable">';
        $html .= '<thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Challan No</th>
                        <th>Vendor</th>
                        <th>Mother Vessel</th>
                        <th>Ghat</th>
                        <th>Destination</th>
                        <th>Old Qty</th>
                        <th>Old Rate</th>
                        <th>Old Amount</th>
                        <th>New Rate</th>
                        <th>New Amount</th>
                        <th>Difference</th>
                        <th>Carrying Bill</th>
                    </tr>
                </thead>';
        $html .= '<tbody>';

        foreach ($programDetails as $detail) {
            $qtyList = [];
            $rateList = []; // Added for Old Rate
            $amountList = [];
            
            foreach ($detail->challanRate as $rate) {
                $qtyList[] = $rate->qty ?? '-';
                $rateList[] = $rate->rate_per_unit ?? '-'; // Capturing rate_per_unit
                $amountList[] = $rate->total ?? '-'; 
            }

            $newDetails = $this->calculateNewChallanDetails($detail, $clientId);
            
            $diffColor = $newDetails['diff'] > 0 ? 'text-danger' : ($newDetails['diff'] < 0 ? 'text-success' : '');
            $diffText = $newDetails['diff'] > 0 ? '+' . $newDetails['diff'] : $newDetails['diff'];

            $html .= '<tr data-id="'.$detail->id.'" style="cursor: pointer;">';
            $html .= '<td>'.($detail->date ?? '-').'</td>';
            $html .= '<td><span class="badge badge-info">'.($detail->challan_no ?? '-').'</span></td>';
            $html .= '<td>'.($detail->vendor->name ?? '-').'</td>';
            $html .= '<td>'.($detail->motherVassel->name ?? '-').'</td>';
            $html .= '<td>'.($detail->ghat->name ?? '-').'</td>';
            $html .= '<td>'.($detail->destination->name ?? '-').'</td>';
            $html .= '<td>'.implode('<br>', $qtyList).'</td>';
            $html .= '<td>'.implode('<br>', $rateList).'</td>'; // Outputting Old Rate
            $html .= '<td>'.implode('<br>', $amountList).'</td>';
            $html .= '<td><strong class="text-primary">'.$newDetails['rate'].'</strong></td>';
            $html .= '<td><strong class="text-primary">'.$newDetails['amount'].'</strong></td>';
            $html .= '<td class="'.$diffColor.'"><strong>'.$diffText.'</strong></td>';
            $html .= '<td>'.($detail->carrying_bill ?? '-').'</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';

        return $html;
    }




}
