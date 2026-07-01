@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- ============================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ============================================= --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-ship mr-2 text-primary"></i>Program Details: {{ $data->motherVassel->name }}
            </h3>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.allProgram') }}" class="btn btn-outline-dark btn-sm"><i class="fas fa-arrow-left mr-1"></i> Back</a>
                
                @if ($data->bill_status == 1)
                    <a href="{{ route('generatingBillShow', $data->id) }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-invoice-dollar mr-1"></i> View Bill</a>
                @else
                    @if(in_array('13', json_decode(auth()->user()->role->permission)))
                        <a href="{{ route('billGenerating', $data->id) }}" class="btn btn-success btn-sm"><i class="fas fa-cogs mr-1"></i> Generate Bill</a>
                    @endif
                @endif

                <a href="{{ route('admin.program.showAddChallan', $data->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-plus mr-1"></i> Add Challan</a>
                <button type="button" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#modal-lg"><i class="fas fa-users mr-1"></i> Vendor Advance</button>
                <button type="button" class="btn btn-outline-warning btn-sm" data-toggle="modal" data-target="#modal-truckSummary"><i class="fas fa-truck mr-1"></i> Truck Summary</button>
                
                @if (Auth::user()->role->name == "All Access")
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#quantitymodal"><i class="fas fa-edit mr-1"></i> Change Qty</button>
                @endif
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- ALERTS --}}
        {{-- ============================================= --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-times-circle mr-2"></i>{{ session('error') }}<button type="button" class="close" data-dismiss="alert">&times;</button></div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">{!! implode('<li>', $errors->all()) !!}</ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        {{-- ============================================= --}}
        {{-- SUMMARY INFO BOXES --}}
        {{-- ============================================= --}}


        @php
            $totalfuelqty = 0; $totalcarrying_bill = 0; $totaladvance = 0; $totalother_cost = 0;
            $totalscale_fee = 0; $totalline_charge = 0; $totaldest_qty = 0;
            foreach ($data->programDetail as $detail) {
                $totalfuelqty += $detail->advancePayment->fuelqty ?? 0;
                $totalcarrying_bill += $detail->carrying_bill ?? 0;
                $totaladvance += $detail->advance ?? 0;
                $totalother_cost += $detail->other_cost ?? 0;
                $totalscale_fee += $detail->scale_fee ?? 0;
                $totalline_charge += $detail->line_charge ?? 0;
                $totaldest_qty += $detail->dest_qty ?? 0;
            }
        @endphp
        
        <div class="row mb-3">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-info"><span class="info-box-icon"><i class="fas fa-boxes"></i></span><div class="info-box-content"><span class="info-box-text">Total Qty</span><span class="info-box-number">{{ number_format($totaldest_qty) }}</span></div></div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-primary"><span class="info-box-icon"><i class="fas fa-file-invoice"></i></span><div class="info-box-content"><span class="info-box-text">Carrying Bill</span><span class="info-box-number">{{ number_format($totalcarrying_bill) }}</span></div></div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-success"><span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span><div class="info-box-content"><span class="info-box-text">Total Advance</span><span class="info-box-number">{{ number_format($totaladvance) }}</span></div></div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-warning"><span class="info-box-icon"><i class="fas fa-gas-pump"></i></span><div class="info-box-content"><span class="info-box-text">Fuel Qty</span><span class="info-box-number">{{ number_format($totalfuelqty) }}</span></div></div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-danger"><span class="info-box-icon"><i class="fas fa-receipt"></i></span><div class="info-box-content"><span class="info-box-text">Line + Scale</span><span class="info-box-number">{{ number_format($totalline_charge + $totalscale_fee) }}</span></div></div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-secondary"><span class="info-box-icon"><i class="fas fa-coins"></i></span><div class="info-box-content"><span class="info-box-text">Other Cost</span><span class="info-box-number">{{ number_format($totalother_cost) }}</span></div></div>
            </div>
        </div>
        

        {{-- ============================================= --}}
        {{-- MAIN TABLE CARD --}}
        {{-- ============================================= --}}



        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-table mr-1"></i> Challan Records</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body p-0">
                
                {{-- Export Buttons --}}
                <div class="px-3 pt-3 pb-2">
                    <button class="btn btn-sm btn-secondary" id="btn-copy"><i class="fas fa-copy"></i> Copy</button>
                    <button class="btn btn-sm btn-success" id="btn-csv"><i class="fas fa-file-csv"></i> CSV</button>
                    <button class="btn btn-sm btn-primary" id="btn-excel"><i class="fas fa-file-excel"></i> Excel</button>
                    <button class="btn btn-sm btn-danger" id="btn-pdf"><i class="fas fa-file-pdf"></i> PDF</button>
                    <button class="btn btn-sm btn-dark" id="btn-print"><i class="fas fa-print"></i> Print</button>
                </div>

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped table-sm mb-0" style="font-size: 12px;">
                        <thead>
                            <tr class="bg-dark text-white text-center">
                                <th style="width:35px">#</th>
                                <th style="width:40px">Bill</th>
                                <th style="width:60px">Pump</th>
                                <th>Bill No</th>
                                <th style="width:85px">Date</th>
                                <th style="width:120px">Vendor</th>
                                <th>Header ID</th>
                                <th style="width:100px">Truck</th>
                                <th>Challan</th>
                                <th style="width:100px">Destination</th>
                                <th>Prev Qty</th>
                                <th>Qty</th>
                                <th>Car. Bill</th>
                                <th>Advance</th>
                                <th>Fuel Qty</th>
                                <th>Fuel Tk</th>
                                <th>Fuel Amt</th>
                                <th>Pump Name</th>
                                <th>Line Ch.</th>
                                <th>Scale</th>
                                <th>Other</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->programDetail as $key => $detail)
                            <tr>
                                <td class="text-center align-middle text-muted">{{ $key + 1 }}</td>
                                
                                <td class="text-center align-middle">
                                    <input type="checkbox" name="checkbox-checked" class="custom-checkbox" @if ($detail->generate_bill == 1) checked @endif>
                                </td>

                                @php
                                    $fuelBills = $detail->advancePayment->petrolPump ?? '' 
                                        ? \App\Models\FuelBill::with('petrolPump:id,name')
                                            ->where('petrol_pump_id', $detail->advancePayment->petrolPump->id)
                                            ->get(['id', 'unique_id', 'qty', 'bill_number', 'petrol_pump_id'])
                                        : collect();
                                @endphp
                                <td class="text-center align-middle">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        @if($detail->fuel_bill_id)
                                            <form action="{{ route('fuel.bill.undo', $detail->id) }}" method="POST" onsubmit="return confirm('Uncheck this item?')" class="m-0 p-0">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-xs p-0 px-1" title="Undo Fuel Bill"><i class="fas fa-undo"></i></button>
                                            </form>
                                        @endif
                                        <input type="checkbox" class="petrol-checkbox" 
                                            data-pump-id="{{ $detail->advancePayment->petrolPump->id ?? '' }}"
                                            data-fuel-bills='@json($fuelBills)'
                                            data-qty="{{ $detail->advancePayment->fuelqty ?? '' }}"
                                            data-program-detail-id="{{ $detail->id }}" 
                                            @if($detail->fuel_bill_id) checked disabled @endif>
                                        @if($detail->fuel_bill_id)
                                            <small class="d-block text-info">{{$detail->fuelBill->unique_id ?? ''}}</small>
                                        @endif
                                    </div>
                                </td>

                                <td class="text-center align-middle">{{ $detail->bill_no }}</td>
                                <td class="text-center align-middle">{{ \Carbon\Carbon::parse($detail->date)->format('d/m/Y') }}</td>
                                <td class="align-middle" title="Create: {{ $detail->createdBy->name ?? '' }} | Update: {{ $detail->updatedBy->name ?? '' }}">{{ $detail->vendor->name }}</td>
                                <td class="text-center align-middle">{{ $detail->headerid }}</td>
                                <td class="text-center align-middle font-weight-bold">{{ strtoupper($detail->truck_number) }}</td>
                                <td class="text-center align-middle">{{ $detail->challan_no }}</td>
                                <td class="align-middle">{{ $detail->destination->name ?? 'N/A' }}</td>
                                <td class="text-center align-middle text-muted">{{ $detail->old_qty }}</td>
                                <td class="text-center align-middle font-weight-bold">{{ $detail->dest_qty }}</td>
                                <td class="text-right align-middle">{{ $detail->carrying_bill }}</td>
                                <td class="text-right align-middle">{{ $detail->advancePayment->cashamount ?? 0 }}</td>
                                <td class="text-right align-middle">{{ $detail->advancePayment->fuelqty ?? 0 }}</td>
                                <td class="text-right align-middle">{{ $detail->advancePayment->fueltoken ?? 0 }}</td>
                                <td class="text-right align-middle">{{ $detail->advancePayment->fuelamount ?? 0 }}</td>
                                <td class="align-middle">{{ $detail->advancePayment->petrolPump->name ?? 'N/A' }}</td>
                                <td class="text-right align-middle">{{ $detail->line_charge }}</td>
                                <td class="text-right align-middle">{{ $detail->scale_fee }}</td>
                                <td class="text-right align-middle">{{ $detail->other_cost }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Petrol Pump Action Footer --}}
            <div class="card-footer bg-light p-2 d-none" id="pump-form-row">
                <form id="pump-action-form" action="{{ route('petrol.pump.mark.qty') }}" method="POST" class="d-flex align-items-center justify-content-center gap-2 mb-0">
                    @csrf
                    <input type="hidden" name="petrol_pump_id" id="petrol_pump_id">
                    <input type="hidden" name="total_qty" id="total_qty">
                    <input type="hidden" id="program_detail_ids" name="program_detail_ids">
                    <label class="font-weight-bold mb-0">Assign Fuel Bill:</label>
                    <select name="unique_id" id="unique-id-display" class="form-control form-control-sm" style="width: 350px;" required>
                        <option value="">Select Unique ID</option>
                    </select>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check-circle mr-1"></i> Submit for Petrol Pump</button>
                </form>
            </div>
        </div>

    </div>
</section>

{{-- ============================================= --}}
{{-- MODALS --}}
{{-- ============================================= }}

{{-- Vendor Advance Modal --}}
<div class="modal fade" id="modal-lg">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h4 class="modal-title"><i class="fas fa-users mr-2"></i>Vendor Advance Summary</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-end gap-2 mb-3">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-sm">Filter by Date</label>
                        <select class="form-control form-control-sm" name="searchdate" id="searchdate" style="width: 200px;">
                            <option value="">All Dates</option>
                            @foreach ($dates as $date)
                                <option value="{{ $date->date }}">{{ $date->date }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" id="dateBtn" class="btn btn-primary btn-sm"><i class="fas fa-search mr-1"></i>Filter</button>
                </div>
                <div class="table-responsive">
                    <table id="example3" class="table table-bordered table-striped table-sm" style="font-size: 12px;">
                        <thead class="text-bold">
                            <tr class="text-center">
                                <th colspan="7"><h3> {{ $data->motherVassel->name }} </h3>
                                <h4 class="text-muted" id="vendorAdvanceSearchDate">Date: All Dates</h4>
                                </th>
                            </tr>
                            <tr class="text-center">
                                <th>#</th><th>Vendor Name</th><th>Count</th><th>Cash Adv</th><th>Fuel Qty</th><th>Fuel Adv</th><th>Total Adv</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($vlist as $key => $v)
                            <tr class="text-center">
                                <td>{{ $key + 1 }}</td>
                                <td class="text-left">{{ $v->vendor_name }}</td>
                                <td>{{ $v->vendor_count }}</td>
                                <td class="text-right">{{ $v->total_cashamount ?? 0 }}</td>
                                <td class="text-right">{{ $v->total_fuelqty ?? 0 }}</td>
                                <td class="text-right">{{ $v->total_fuelamount ?? 0 }}</td>
                                <td class="text-right font-weight-bold">{{ $v->total_amount ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light font-weight-bold">
                            <tr class="text-center text-dark">
                                <td colspan="2">TOTAL</td>
                                <td>{{ $vlist->sum('vendor_count') }}</td>
                                <td class="text-right">{{ $vlist->sum('total_cashamount') }}</td>
                                <td class="text-right">{{ $vlist->sum('total_fuelqty') }}</td>
                                <td class="text-right">{{ $vlist->sum('total_fuelamount') }}</td>
                                <td class="text-right">{{ $vlist->sum('total_amount') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Truck Summary Modal --}}
<div class="modal fade" id="modal-truckSummary">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h4 class="modal-title"><i class="fas fa-truck mr-2"></i>Truck Summary</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-end gap-2 mb-3">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-sm">Vendor</label>
                        <select class="form-control form-control-sm" name="vendors_truc" id="vendors_truc" style="width: 200px;">
                            <option value="">All Vendors</option>
                            @foreach ($vlist as $vendor)
                                <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="font-weight-bold text-sm">Date</label>
                        <select class="form-control form-control-sm" name="trucksearchdate" id="trucksearchdate" style="width: 200px;">
                            <option value="">All Dates</option>
                            @foreach ($dates as $date)
                                <option value="{{ $date->date }}">{{ $date->date }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" id="vtrucBtn" class="btn btn-primary btn-sm"><i class="fas fa-search mr-1"></i>Filter</button>
                </div>
                <div class="table-responsive">
                    <table id="example4" class="table table-bordered table-striped table-sm vendorsummery" style="font-size: 12px;">
                        <thead class="text-bold">
                            
                            <tr class="text-center">
                                <th colspan="7"><h3> {{ $data->motherVassel->name }} </h3>
                                <h4 class="text-muted" id="truckSummarySearchDate">Date: All Dates</h4>
                                </th>
                            </tr>
                            <tr class="text-center"><th>#</th><th>Truck Number</th><th>Count</th><th>Cash Adv</th><th>Fuel Qty</th><th>Fuel Adv</th><th>Total Adv</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($truckSummary as $key => $t)
                            <tr class="text-center">
                                <td>{{ $key + 1 }}</td>
                                <td class="text-left font-weight-bold">{{ $t->truck_number ?? '' }}</td>
                                <td>{{ $t->vehicle_count ?? 0 }}</td>
                                <td class="text-right">{{ $t->total_cashamount ?? 0 }}</td>
                                <td class="text-right">{{ $t->total_fuelqty ?? 0 }}</td>
                                <td class="text-right">{{ $t->total_fuelamount ?? 0 }}</td>
                                <td class="text-right font-weight-bold">{{ $t->total_amount ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if (Auth::user()->role->name == "All Access")
{{-- Quantity Change Modal --}}
<div class="modal fade" id="quantitymodal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h4 class="modal-title">Change Quantity</h4>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="font-weight-bold text-sm">New Quantity</label>
                    <input type="number" name="newQty" id="newQty" value="12" class="form-control form-control-sm">
                    <input type="hidden" name="type" id="type" value="{{ $type ?? '' }}">
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" id="undoBtn" class="btn btn-default btn-sm">Undo</button>
                    <button type="button" id="qtyBtn" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<input type="hidden" name="program_id" id="program_id" value="{{ $data->id }}">
@endsection

@section('style')
<style>
    .info-box .info-box-number { font-size: 16px !important; }
    .custom-checkbox { width: 18px; height: 18px; cursor: pointer; }
</style>
@endsection

@section('script')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
 $(document).ready(function () {
    // =============================================
    // DATATABLE INIT
    // =============================================
    var programTBL = $('#example1').DataTable({
        responsive: true, lengthChange: true, autoWidth: false, pageLength: 100,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-secondary', text: '<i class="fas fa-copy"></i> Copy' },
            { extend: 'csv', className: 'btn btn-sm btn-success', text: '<i class="fas fa-file-csv"></i> CSV' },
            { extend: 'excel', className: 'btn btn-sm btn-primary', text: '<i class="fas fa-file-excel"></i> Excel' },
            { extend: 'pdf', className: 'btn btn-sm btn-danger', text: '<i class="fas fa-file-pdf"></i> PDF', title: 'Program_Details' },
            { extend: 'print', className: 'btn btn-sm btn-dark', text: '<i class="fas fa-print"></i> Print' }
        ],
        order: [], lengthMenu: [[100, "All", 50, 25], [100, "All", 50, 25]],
        language: { search: "", searchPlaceholder: "Search details..." }
    });
    $('.dt-buttons').hide();
    $('#btn-copy').on('click', function() { programTBL.button(0).trigger(); });
    $('#btn-csv').on('click', function() { programTBL.button(1).trigger(); });
    $('#btn-excel').on('click', function() { programTBL.button(2).trigger(); });
    $('#btn-pdf').on('click', function() { programTBL.button(3).trigger(); });
    $('#btn-print').on('click', function() { programTBL.button(4).trigger(); });

    // Modals Tables Init
    $('#example3').DataTable({ responsive: true, lengthChange: false, autoWidth: false, dom: 'Bfrtip', order: [], lengthMenu: [[100, "All"], [100, "All"]], buttons: ["copy","csv","excel","print"] }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
    $('.vendorsummery').DataTable({ responsive: true, lengthChange: false, autoWidth: false, dom: 'Bfrtip', order: [], lengthMenu: [[100, "All"], [100, "All"]], buttons: ["copy","csv","excel","print"] }).buttons().container().appendTo('#vendorsummery_wrapper .col-md-6:eq(0)');
    $('.dt-buttons').hide(); // Hide modal button bars to keep it clean, or keep if you want them inside modals

    // =============================================
    // PETROL PUMP CHECKBOX LOGIC
    // =============================================
    let selectedRows = {};
    let selectedPumpId = null;

    $(document).on('change', '.petrol-checkbox', function () {
        const checkbox = $(this);
        const programDetailId = checkbox.data('program-detail-id');
        const pumpId = checkbox.data('pump-id');
        const fuelBills = checkbox.data('fuel-bills');
        const qty = parseFloat(checkbox.data('qty')) || 0;

        if (this.checked) {
            if (!selectedPumpId) { selectedPumpId = pumpId; } 
            else if (selectedPumpId !== pumpId) { alert('Only the same petrol pump can be selected!'); checkbox.prop('checked', false); return; }
            selectedRows[programDetailId] = { pumpId, fuelBills, qty };
        } else {
            delete selectedRows[programDetailId];
            if (Object.keys(selectedRows).length === 0) selectedPumpId = null;
        }

        const selectedCount = Object.keys(selectedRows).length;
        if (selectedCount > 0) {
            $('#pump-form-row').removeClass('d-none');
            $('#petrol_pump_id').val(selectedPumpId);
            
            let optionsHtml = `<option value="">Select Unique ID</option>`;
            fuelBills.forEach(fb => { optionsHtml += `<option value="${fb.unique_id}">${fb.unique_id} - ${fb.petrol_pump.name} - ${fb.qty}L</option>`; });
            $('#unique-id-display').html(optionsHtml);

            let totalQty = 0; const selectedIds = [];
            Object.keys(selectedRows).forEach(id => { totalQty += selectedRows[id].qty; selectedIds.push(id); });
            $('#total_qty').val(totalQty);
            $('#program_detail_ids').val(JSON.stringify(selectedIds));
        } else {
            $('#pump-form-row').addClass('d-none');
            $('#unique-id-display').empty();
        }
    });

    // =============================================
    // VENDOR ADVANCE MODAL SEARCH (AJAX)
    // =============================================
    $('#dateBtn').click(function() {
        var selectedDate = $('#searchdate').val(); // Can be empty string for "All Dates"
        var program_id = $('#program_id').val();
        

        $.ajax({
            url: '{{ route("getAdvancePayments") }}', 
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}',
                date: selectedDate, 
                program_id: program_id 
            },
            beforeSend: function() {
                $('#dateBtn').html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...').prop('disabled', true);
            },
            success: function(response) {
                if ($.fn.DataTable.isDataTable('#example3')) { 
                    $('#example3').DataTable().destroy(); 
                }
                
                var tbody = $('#example3 tbody'); 
                tbody.empty();
                var tfoot = $('#example3 tfoot'); 
                tfoot.empty();

                $('#vendorAdvanceSearchDate').text(selectedDate ? `Date: ${selectedDate}` : 'All Dates');
                
                // Handle empty data gracefully
                if(response.data.length === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted py-3">No data found for this date.</td></tr>');
                } else {
                    $.each(response.data, function(i, p) {
                        tbody.append(`<tr class="text-center">
                            <td>${i+1}</td>
                            <td class="text-left">${p.vendor?.name ?? ''}</td>
                            <td>${p.vendor_count}</td>
                            <td class="text-right">${p.total_cashamount || 0}</td>
                            <td class="text-right">${p.total_fuelqty || 0}</td>
                            <td class="text-right">${p.total_fuelamount || 0}</td>
                            <td class="text-right font-weight-bold">${p.total_amount || 0}</td>
                        </tr>`);
                    });

                    tfoot.append(`<tr class="text-center text-dark bg-light font-weight-bold">
                        <td colspan="2">TOTAL</td>
                        <td>${response.data.reduce((s,p) => s + (p.vendor_count || 0), 0)}</td>
                        <td class="text-right">${response.data.reduce((s,p) => s + (p.total_cashamount || 0), 0)}</td>
                        <td class="text-right">${response.data.reduce((s,p) => s + (p.total_fuelqty || 0), 0)}</td>
                        <td class="text-right">${response.data.reduce((s,p) => s + (p.total_fuelamount || 0), 0)}</td>
                        <td class="text-right">${response.data.reduce((s,p) => s + (p.total_amount || 0), 0)}</td>
                    </tr>`);
                }
                
                $('#example3').DataTable({ 
                    responsive: true, lengthChange: false, autoWidth: false, 
                    dom: 'Bfrtip', order: [], retrieve: true, 
                    lengthMenu: [[100, "All"], [100, "All"]], 
                    buttons: ["copy","csv","excel","print"] 
                }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
                
                $('.dt-buttons').hide(); // Hide default buttons
            },
            error: function(xhr) {
                console.log("Vendor Advance Error:", xhr.responseJSON); // Added this
                alert("Error loading vendor advance data.");
            },
            complete: function() {
                $('#dateBtn').html('<i class="fas fa-search mr-1"></i>Filter').prop('disabled', false);
            }
        });
    });


    // =============================================
    // TRUCK SUMMARY MODAL SEARCH (AJAX)
    // =============================================
    $('#vtrucBtn').click(function() {
        var selectedVendor = $('#vendors_truc').val();
        var selectedDate = $('#trucksearchdate').val();
        var program_id = $('#program_id').val();
        
        // Assuming you have a route named 'getTruckSummary' setup in web.php
        // If your route name is different, change it below
        $.ajax({
            url: '{{ route("getProgramDetailsByVendor") }}', 
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}',
                vendor_id: selectedVendor, 
                date: selectedDate, 
                program_id: program_id 
            },
            beforeSend: function() {
                $('#vtrucBtn').html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...').prop('disabled', true);
            },
            success: function(response) {
                if ($.fn.DataTable.isDataTable('#example4')) { 
                    $('#example4').DataTable().destroy(); 
                }
                
                var tbody = $('#example4 tbody'); 
                tbody.empty();
                
                $('#truckSummarySearchDate').text(selectedDate ? `Date: ${selectedDate}` : 'All Dates');

                if(response.data.length === 0) {
                    tbody.append('<tr><td colspan="7" class="text-center text-muted py-3">No data found.</td></tr>');
                } else {
                    $.each(response.data, function(i, t) {
                        tbody.append(`<tr class="text-center">
                            <td>${i+1}</td>
                            <td class="text-left font-weight-bold">${t.truck_number || ''}</td>
                            <td>${t.vehicle_count || 0}</td>
                            <td class="text-right">${t.total_cashamount || 0}</td>
                            <td class="text-right">${t.total_fuelqty || 0}</td>
                            <td class="text-right">${t.total_fuelamount || 0}</td>
                            <td class="text-right font-weight-bold">${t.total_amount || 0}</td>
                        </tr>`);
                    });
                }
                
                $('#example4').DataTable({ 
                    responsive: true, lengthChange: false, autoWidth: false, 
                    dom: 'Bfrtip', order: [], retrieve: true, 
                    lengthMenu: [[100, "All"], [100, "All"]], 
                    buttons: ["copy","csv","excel","print"] 
                }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
                
                $('.dt-buttons').hide();
            },
            error: function(xhr) {
                alert("Error loading truck summary data. Make sure the route exists.");
                console.log(xhr.responseText);
            },
            complete: function() {
                $('#vtrucBtn').html('<i class="fas fa-search mr-1"></i>Filter').prop('disabled', false);
            }
        });
    });

    // =============================================
    // QTY CHANGE LOGIC (Assuming route exists)
    // =============================================
    $('#qtyBtn').click(function() {
        var newQty = $('#newQty').val();
        var type = $('#type').val();
        var program_id = $('#program_id').val();
        if (!newQty) { alert('Quantity is required'); return; }

        $.ajax({
            url: '{{ route("changeQuantity") }}', method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}',
                newQty: newQty, 
                program_id: program_id, 
                type: type 
            },
            success: function(response) { if(response.status == 200) { location.reload(); } }
        });
    });


    $('#undoBtn').click(function() {
            $(this).attr('disabled', true);
            $('#loader').show();
        var program_id = $('#program_id').val();

        $.ajax({
            url: '{{ route("undoChangeQuantity") }}',
            method: 'POST',
            data: {program_id: program_id},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log(response);
                if (response.status == 200) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Failed to update quantity');
                }
            },
            error: function(xhr, status, error) {
                console.log(xhr.responseJSON.message);
            }
        });
    });
    // (Add your #vtrucBtn and #undoBtn logic here exactly as it was in your original file, just ensuring it uses the new clean selectors)

});
</script>
@endsection