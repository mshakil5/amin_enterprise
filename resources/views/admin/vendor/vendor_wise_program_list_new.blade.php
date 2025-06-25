@extends('admin.layouts.admin')

@section('content')

<style>
    /* Optimize checkbox styling */
    .form-checkbox {
        display: grid;
        grid-template-columns: 1em auto;
        gap: 0.3em;
        align-items: center;
    }

    .custom-checkbox {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    /* Table-specific styles */
    .table-container {
        overflow-x: auto;
        max-width: 100%;
        margin-bottom: 20px;
    }

    .datatable {
        width: 100% !important;
        font-size: 0.9rem;
        border-collapse: collapse;
    }

    .datatable th, .datatable td {
        padding: 8px;
        text-align: center;
        white-space: nowrap;
        border: 1px solid #dee2e6;
    }

    /* Specific column widths */
    .datatable th:nth-child(1), .datatable td:nth-child(1) { width: 40px; } /* Sl */
    .datatable th:nth-child(2), .datatable td:nth-child(2) { width: 50px; } /* Bill Status */
    .datatable th:nth-child(3), .datatable td:nth-child(3) { width: 60px; } /* Petrol Pump */
    .datatable th:nth-child(4), .datatable td:nth-child(4) { width: 100px; } /* Bill No */
    .datatable th:nth-child(5), .datatable td:nth-child(5) { width: 80px; } /* Date */
    .datatable th:nth-child(6), .datatable td:nth-child(6) { width: 120px; } /* Vendor */
    .datatable th:nth-child(7), .datatable td:nth-child(7) { width: 80px; } /* Header ID */
    .datatable th:nth-child(8), .datatable td:nth-child(8) { width: 100px; } /* Truck Number */
    .datatable th:nth-child(9), .datatable td:nth-child(9) { width: 100px; } /* Challan No */
    .datatable th:nth-child(10), .datatable td:nth-child(10) { width: 120px; } /* Destination */
    .datatable th:nth-child(11), .datatable td:nth-child(11) { width: 80px; } /* Qty */
    .datatable th:nth-child(12), .datatable td:nth-child(12) { width: 100px; } /* Carrying Bill */
    .datatable th:nth-child(13), .datatable td:nth-child(13) { width: 80px; } /* Line Charge */
    .datatable th:nth-child(14), .datatable td:nth-child(14) { width: 80px; } /* Scale Fee */
    .datatable th:nth-child(15), .datatable td:nth-child(15) { width: 80px; } /* Other Cost */
    .datatable th:nth-child(16), .datatable td:nth-child(16) { width: 100px; } /* Cash Advance */
    .datatable th:nth-child(17), .datatable td:nth-child(17) { width: 80px; } /* Fuel Qty */
    .datatable th:nth-child(18), .datatable td:nth-child(18) { width: 100px; } /* Fuel Amount */
    .datatable th:nth-child(19), .datatable td:nth-child(19) { width: 100px; } /* Fuel Token */
    .datatable th:nth-child(20), .datatable td:nth-child(20) { width: 120px; } /* Pump Name */

    /* Sticky headers */
    .datatable thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
        font-weight: 600;
    }

    /* Fixed first two columns */
    .datatable th:nth-child(1), .datatable td:nth-child(1),
    .datatable th:nth-child(2), .datatable td:nth-child(2) {
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 5;
    }

    .datatable th:nth-child(2), .datatable td:nth-child(2) {
        left: 40px;
    }

    /* Button loading state */
    .download-excel-btn.loading::after {
        content: ' Loading...';
        font-style: italic;
    }
</style>

<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <a href="{{ route('admin.vendor') }}" class="btn btn-secondary my-3">Back</a>
            </div>
        </div>
    </div>
</section>

<!-- Tabs for switching between sections -->
<div class="container-fluid mb-3">
    <ul class="nav nav-tabs" id="vendorTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link active" id="sequence-tab" data-toggle="tab" href="#sequence" role="tab" aria-controls="sequence" aria-selected="true">
                Mother Vessel Wise Trip List
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link" id="all-data-tab" data-toggle="tab" href="#all-data" role="tab" aria-controls="all-data" aria-selected="false">
                All Trip List
            </a>
        </li>
    </ul>
</div>

<div class="tab-content" id="vendorTabContent">
    <!-- Sequence Tab -->
    <div class="tab-pane fade show active" id="sequence" role="tabpanel" aria-labelledby="sequence-tab">
        <section class="content" id="contentContainer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">Vendor Trip List</h3>
                            </div>
                            <div class="card-body">
                                @if (empty($data))
                                    <div class="alert alert-warning">No trip data available for this vendor.</div>
                                @else
                                    @foreach ($data as $motherVassel => $pdtl)
                                        @php
                                            $totalfuelamount = 0;
                                            $totalcashamount = 0;
                                            $totalfuelqty = 0;
                                            $totalcarrying_bill = 0;
                                            $totaladvance = 0;
                                            $totalother_cost = 0;
                                            $totalscale_fee = 0;
                                            $totalline_charge = 0;
                                            $totaldest_qty = 0;

                                            $motherVesselNAme = \App\Models\MotherVassel::where('id', $motherVassel)->first();
                                            // $motherVassel = $motherVesselNAme ? $motherVesselNAme->name : 'N/A';
                                        @endphp

                                        <div style="text-align: center; margin-bottom: 20px;">
                                            <h4>Vendor: {{ $vendor->name ?? 'N/A' }}</h4>
                                            <h5>Sequence Number: {{ $vendorSequenceNumber->unique_id ?? 'N/A' }}</h5>
                                            <h5>Mother Vessel: {{ $motherVassel ?? $motherVassel ?? 'N/A' }}</h5>
                                        </div>

                                        <!-- Download Excel Button -->
                                        <div class="mb-3">
                                            <button class="btn btn-success download-excel-btn"
                                                    data-tab="sequence"
                                                    data-vendor="{{ $vendor->id ?? 'N/A' }}"
                                                    data-sequence-number="{{ $vendorSequenceNumber->unique_id ?? 'N/A' }}"
                                                    data-mother-vessel="{{ $motherVassel }}">
                                                Download Excel
                                            </button>
                                        </div>

                                        <div class="table-container">
                                            <table class="table table-bordered table-striped datatable">
                                                <thead>
                                                    <tr>
                                                        <th>Sl</th>
                                                        <th>Bill<br>Status</th>
                                                        <th>Petrol<br>Pump</th>
                                                        <th>Bill No</th>
                                                        <th>Date</th>
                                                        <th>Vendor</th>
                                                        <th>Header ID</th>
                                                        <th>Truck No</th>
                                                        <th>Challan No</th>
                                                        <th>Destination</th>
                                                        <th>Qty</th>
                                                        <th>Carrying Bill</th>
                                                        <th>Line Charge</th>
                                                        <th>Scale Fee</th>
                                                        <th>Other Cost</th>
                                                        <th>Cash Adv</th>
                                                        <th>Fuel Qty</th>
                                                        <th>Fuel Amt</th>
                                                        <th>Fuel Token</th>
                                                        <th>Pump Name</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($pdtl as $key => $data)
                                                        <tr>
                                                            <td>{{ $key + 1 }}</td>
                                                            <td>
                                                                <label class="form-checkbox">
                                                                    <input type="checkbox" name="checkbox-checked" class="custom-checkbox"
                                                                           @if ($data->generate_bill == 1) checked @endif />
                                                                </label>
                                                            </td>
                                                            @php
                                                                $fuelBills = $data->advancePayment->petrolPump ?? null
                                                                    ? \App\Models\FuelBill::with('petrolPump:id,name')
                                                                        ->where('petrol_pump_id', $data->advancePayment->petrolPump->id)
                                                                        ->get(['id', 'unique_id', 'qty', 'bill_number', 'petrol_pump_id'])
                                                                    : collect();
                                                            @endphp
                                                            <td>
                                                                <label class="form-checkbox">
                                                                    <input type="checkbox" class="petrol-checkbox custom-checkbox"
                                                                           data-pump-id="{{ $data->advancePayment->petrolPump->id ?? '' }}"
                                                                           data-fuel-bills='@json($fuelBills)'
                                                                           data-qty="{{ $data->advancePayment->fuelqty ?? 0 }}"
                                                                           data-program-detail-id="{{ $data->id }}"
                                                                           @if($data->fuel_bill_id) checked disabled @endif>
                                                                </label>
                                                            </td>
                                                            <td>{{ $data->bill_no ?? 'N/A' }}</td>
                                                            <td>{{ $data->date ? \Carbon\Carbon::parse($data->date)->format('d/m/Y') : 'N/A' }}</td>
                                                            <td>{{ $data->vendor->name ?? 'N/A' }}</td>
                                                            <td>{{ $data->headerid ?? 'N/A' }}</td>
                                                            <td>{{ strtoupper($data->truck_number ?? '') }}</td>
                                                            <td>{{ $data->challan_no ?? 'N/A' }}</td>
                                                            <td>{{ $data->destination->name ?? 'N/A' }}</td>
                                                            <td>{{ number_format($data->dest_qty ?? 0, 2) }}</td>
                                                            <td>{{ number_format($data->carrying_bill ?? 0, 2) }}</td>
                                                            <td>{{ number_format($data->line_charge ?? 0, 2) }}</td>
                                                            <td>{{ number_format($data->scale_fee ?? 0, 2) }}</td>
                                                            <td>{{ number_format($data->other_cost ?? 0, 2) }}</td>
                                                            <td>{{ isset($data->advancePayment->cashamount) ? number_format($data->advancePayment->cashamount, 2) : '0.00' }}</td>
                                                            <td>{{ isset($data->advancePayment->fuelqty) ? number_format($data->advancePayment->fuelqty, 2) : '0.00' }}</td>
                                                            <td>{{ isset($data->advancePayment->fuelamount) ? number_format($data->advancePayment->fuelamount, 2) : '0.00' }}</td>
                                                            <td>{{ $data->advancePayment->fueltoken ?? 'N/A' }}</td>
                                                            <td>{{ $data->advancePayment->petrolPump->name ?? 'N/A' }}</td>
                                                            @php
                                                                $totalfuelamount += $data->advancePayment->fuelamount ?? 0;
                                                                $totalcashamount += $data->advancePayment->cashamount ?? 0;
                                                                $totalfuelqty += $data->advancePayment->fuelqty ?? 0;
                                                                $totalcarrying_bill += $data->carrying_bill ?? 0;
                                                                $totaladvance += $data->advance ?? 0;
                                                                $totalother_cost += $data->other_cost ?? 0;
                                                                $totalscale_fee += $data->scale_fee ?? 0;
                                                                $totalline_charge += $data->line_charge ?? 0;
                                                                $totaldest_qty += $data->dest_qty ?? 0;
                                                            @endphp
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ number_format($totaldest_qty, 2) }}</td>
                                                        <td>{{ number_format($totalcarrying_bill, 2) }}</td>
                                                        <td>{{ number_format($totalline_charge, 2) }}</td>
                                                        <td>{{ number_format($totalscale_fee, 2) }}</td>
                                                        <td>{{ number_format($totalother_cost, 2) }}</td>
                                                        <td>{{ number_format($totalcashamount, 2) }}</td>
                                                        <td>{{ number_format($totalfuelqty, 2) }}</td>
                                                        <td>{{ number_format($totalfuelamount, 2) }}</td>
                                                        <td></td>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5"></td>
                                                        <td colspan="5">
                                                            <b>Total Adv:</b> {{ number_format($totalcashamount + $totalfuelamount, 2) }}
                                                        </td>
                                                        <td colspan="8">
                                                            <strong>Total Vendor's Payable: {{ number_format($totalcarrying_bill + $totalscale_fee, 2) }} - {{ number_format($totalcashamount + $totalfuelamount, 2) }} = {{ number_format($totalcarrying_bill + $totalscale_fee - $totalcashamount - $totalfuelamount, 2) }}</strong>
                                                        </td>
                                                        <td colspan="2"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- All Data Tab -->
    <div class="tab-pane fade" id="all-data" role="tabpanel" aria-labelledby="all-data-tab">
        <section class="content" id="contentContainer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">Vendor Trip List (Sequence Wise All Data)</h3>
                            </div>
                            <div class="card-body">
                                <div style="text-align: center; margin-bottom: 20px;">
                                    <h4>Vendor: {{ $vendor->name ?? 'N/A' }}</h4>
                                    <h5>Sequence Number: {{ $vendorSequenceNumber->unique_id ?? 'N/A' }}</h5>
                                </div>

                                <!-- Download Excel Button -->
                                <div class="mb-3">
                                    <button class="btn btn-success download-excel-btn"
                                            data-tab="all-data"
                                            data-vendor="{{ $vendor->id ?? 'N/A' }}"
                                            data-sequence-number="{{ $vendorSequenceNumber->unique_id ?? 'N/A' }}"
                                            data-mother-vessel="All Trips">
                                        Download Excel
                                    </button>
                                </div>

                                <div class="table-container">
                                    <table class="table table-bordered table-striped datatable">
                                        <thead>
                                            <tr>
                                                <th>Sl</th>
                                                <th>Petrol Pump</th>
                                                <th>Date</th>
                                                <th>Vendor</th>
                                                <th>Header ID</th>
                                                <th>Truck Number</th>
                                                <th>Challan No</th>
                                                <th>Mother Vessel</th>
                                                <th>Destination</th>
                                                <th>Qty</th>
                                                <th>Carrying Bill</th>
                                                <th>Line Charge</th>
                                                <th>Scale Fee</th>
                                                <th>Other Cost</th>
                                                <th>Cash Advance</th>
                                                <th>Fuel Qty</th>
                                                <th>Fuel Amount</th>
                                                <th>Fuel Token</th>
                                                <th>Pump Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $alltotalfuelamount = 0;
                                                $alltotalcashamount = 0;
                                                $alltotalfuelqty = 0;
                                                $alltotalcarrying_bill = 0;
                                                $alltotaladvance = 0;
                                                $alltotalother_cost = 0;
                                                $alltotalscale_fee = 0;
                                                $alltotalline_charge = 0;
                                                $alltotaldest_qty = 0;
                                            @endphp
                                            @if (empty($alldata))
                                                <tr>
                                                    <td colspan="19" class="text-center">No trip data available.</td>
                                                </tr>
                                            @else
                                                @foreach ($alldata as $key => $data)
                                                    <tr>
                                                        <td>{{ $key + 1 }}</td>
                                                        @php
                                                            $fuelBills = $data->advancePayment->petrolPump ?? null
                                                                ? \App\Models\FuelBill::with('petrolPump:id,name')
                                                                    ->where('petrol_pump_id', $data->advancePayment->petrolPump->id)
                                                                    ->get(['id', 'unique_id', 'qty', 'bill_number', 'petrol_pump_id'])
                                                                : collect();
                                                        @endphp
                                                        <td>
                                                            <label class="form-checkbox">
                                                                <input type="checkbox" class="petrol-checkbox custom-checkbox"
                                                                       data-pump-id="{{ $data->advancePayment->petrolPump->id ?? '' }}"
                                                                       data-fuel-bills='@json($fuelBills)'
                                                                       data-qty="{{ $data->advancePayment->fuelqty ?? 0 }}"
                                                                       data-program-detail-id="{{ $data->id }}"
                                                                       @if($data->fuel_bill_id) checked disabled @endif>
                                                            </label>
                                                        </td>
                                                        <td>{{ $data->date ? \Carbon\Carbon::parse($data->date)->format('d/m/Y') : 'N/A' }}</td>
                                                        <td>{{ $data->vendor->name ?? 'N/A' }}</td>
                                                        <td>{{ $data->headerid ?? 'N/A' }}</td>
                                                        <td>{{ strtoupper($data->truck_number ?? '') }}</td>
                                                        <td>{{ $data->challan_no ?? 'N/A' }}</td>
                                                        <td>{{ $data->motherVassel->name ?? 'N/A' }}</td>
                                                        <td>{{ $data->destination->name ?? 'N/A' }}</td>
                                                        <td>{{ number_format($data->dest_qty ?? 0, 2) }}</td>
                                                        <td>{{ number_format($data->carrying_bill ?? 0, 2) }}</td>
                                                        <td>{{ number_format($data->line_charge ?? 0, 2) }}</td>
                                                        <td>{{ number_format($data->scale_fee ?? 0, 2) }}</td>
                                                        <td>{{ number_format($data->other_cost ?? 0, 2) }}</td>
                                                        <td>{{ isset($data->advancePayment->cashamount) ? number_format($data->advancePayment->cashamount, 2) : '0.00' }}</td>
                                                        <td>{{ isset($data->advancePayment->fuelqty) ? number_format($data->advancePayment->fuelqty, 2) : '0.00' }}</td>
                                                        <td>{{ isset($data->advancePayment->fuelamount) ? number_format($data->advancePayment->fuelamount, 2) : '0.00' }}</td>
                                                        <td>{{ $data->advancePayment->fueltoken ?? 'N/A' }}</td>
                                                        <td>{{ $data->advancePayment->petrolPump->name ?? 'N/A' }}</td>
                                                        @php
                                                            $alltotalfuelamount += $data->advancePayment->fuelamount ?? 0;
                                                            $alltotalcashamount += $data->advancePayment->cashamount ?? 0;
                                                            $alltotalfuelqty += $data->advancePayment->fuelqty ?? 0;
                                                            $alltotalcarrying_bill += $data->carrying_bill ?? 0;
                                                            $alltotaladvance += $data->advance ?? 0;
                                                            $alltotalother_cost += $data->other_cost ?? 0;
                                                            $alltotalscale_fee += $data->scale_fee ?? 0;
                                                            $alltotalline_charge += $data->line_charge ?? 0;
                                                            $alltotaldest_qty += $data->dest_qty ?? 0;
                                                        @endphp
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="2"></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td>{{ number_format($alltotaldest_qty, 2) }}</td>
                                                <td>{{ number_format($alltotalcarrying_bill, 2) }}</td>
                                                <td>{{ number_format($alltotalline_charge, 2) }}</td>
                                                <td>{{ number_format($alltotalscale_fee, 2) }}</td>
                                                <td>{{ number_format($alltotalother_cost, 2) }}</td>
                                                <td>{{ number_format($alltotalcashamount, 2) }}</td>
                                                <td>{{ number_format($alltotalfuelqty, 2) }}</td>
                                                <td>{{ number_format($alltotalfuelamount, 2) }}</td>
                                                <td><b>Total Adv:</b></td>
                                                <td><b>{{ number_format($alltotaladvance, 2) }}</b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td colspan="5">
                                                    <b>Total Adv:</b> {{ number_format($alltotalcashamount + $alltotalfuelamount, 2) }}
                                                </td>
                                                <td colspan="8">
                                                    @php
                                                        $totalPayable = $alltotalcarrying_bill + $alltotalscale_fee - $alltotalcashamount - $alltotalfuelamount;
                                                    @endphp
                                                    <strong @if($totalPayable < 0) style="background-color: #ffcccc;" @endif>
                                                        Total Vendor's Payable: {{ number_format($alltotalcarrying_bill + $alltotalscale_fee, 2) }} - {{ number_format($alltotalcashamount + $alltotalfuelamount, 2) }} = {{ number_format($totalPayable, 2) }}
                                                    </strong>
                                                </td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection

@section('script')

<script>
    (function($) {
        // Ensure jQuery is available
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded. Please ensure jQuery is included in the layout.');
            return;
        }

        $(document).ready(function () {
            // CSRF Token Setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || ''
                }
            });

            // Download Excel Button Click Handler
            $('.download-excel-btn').on('click', function () {
                const $btn = $(this);
                $btn.addClass('loading').prop('disabled', true);

                const tab = $btn.data('tab');
                const vendor = $btn.data('vendor');
                const sequenceNumber = $btn.data('sequence-number');
                const motherVessel = $btn.data('mother-vessel');

                console.log('Download button clicked:', { tab, vendor, sequenceNumber, motherVessel });
                // Validate data attributes
                if (!tab || !vendor || !sequenceNumber) {
                    console.error('Missing data attributes:', { tab, vendor, sequenceNumber, motherVessel });
                    alert('Error: Missing required data for export.');
                    $btn.removeClass('loading').prop('disabled', false);
                    return;
                }

                $.ajax({
                    url: '{{ route("admin.vendor-trip.export-excel") }}',
                    method: 'POST',
                    data: {
                        tab: tab,
                        vendor: vendor,
                        sequence_number: sequenceNumber,
                        mother_vessel: motherVessel
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function (data, status, xhr) {
                        const blob = new Blob([data], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                        const disposition = xhr.getResponseHeader('Content-Disposition');
                        let filename = 'vendor_trips_' + sequenceNumber + '.xlsx';
                        if (disposition && disposition.includes('filename=')) {
                            const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                            if (matches && matches[1]) {
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }
                        const link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = filename;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        $btn.removeClass('loading').prop('disabled', false);
                    },
                    error: function (xhr) {
                        console.error('Excel download failed:', xhr);
                        let message = 'Unknown error';
                        if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.statusText) {
                            message = xhr.statusText;
                        }
                        alert('Error downloading Excel file: ' + message);
                        $btn.removeClass('loading').prop('disabled', false);
                    }
                });
            });

            // Petrol Checkbox Logic
            let selectedPumpId = null;

            $('.petrol-checkbox').on('change', function () {
                const currentPumpId = $(this).data('pump-id');
                const fuelBills = $(this).data('fuel-bills') || [];

                if (this.checked) {
                    if (!selectedPumpId) {
                        selectedPumpId = currentPumpId;
                    }

                    if (selectedPumpId !== currentPumpId) {
                        alert('Only same petrol pump can be selected!');
                        $(this).prop('checked', false);
                        return;
                    }
                } else {
                    if ($('.petrol-checkbox:checked').length === 0) {
                        selectedPumpId = null;
                    }
                }

                const checkedBoxes = $('.petrol-checkbox:checked');
                if (checkedBoxes.length > 0) {
                    $('#pump-form-row').show();
                    $('#petrol_pump_id').val(selectedPumpId);

                    let optionsHtml = `<option value="">Select Unique ID</option>`;
                    fuelBills.forEach(fb => {
                        optionsHtml += `<option value="${fb.unique_id}">
                            ${fb.unique_id} - ${fb.petrol_pump?.name ?? 'N/A'} - ${fb.qty}L - Bill#${fb.bill_number}
                        </option>`;
                    });
                    $('#unique-id-display').html(optionsHtml);

                    let totalQty = 0;
                    let selectedProgramDetailIds = [];
                    checkedBoxes.each(function () {
                        totalQty += parseFloat($(this).data('qty')) || 0;
                        const progId = $(this).data('program-detail-id');
                        if (progId) selectedProgramDetailIds.push(progId);
                    });

                    $('#total_qty').val(totalQty.toFixed(2));
                    $('#program_detail_ids').val(JSON.stringify(selectedProgramDetailIds));
                } else {
                    $('#pump-form-row').hide();
                    $('#unique-id-display').empty();
                    $('#total_qty').val('');
                    $('#program_detail_ids').val('');
                }
            });
        });
    })(jQuery);
</script>

@endsection