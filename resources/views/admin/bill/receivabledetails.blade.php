@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Receivable Details
            </h3>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        {{-- Summary Info Boxes --}}
        <div class="row mb-3">
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Date</span>
                        <span class="info-box-number" style="font-size:14px">{{ $billReceive->date }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-university"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Rcv Type</span>
                        <span class="info-box-number" style="font-size:14px">{{ $billReceive->rcv_type }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-weight"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Qty</span>
                        <span class="info-box-number">{{ $billReceive->qty }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Amount</span>
                        <span class="info-box-number">{{ number_format($billReceive->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Net Amount</span>
                        <span class="info-box-number">{{ number_format($billReceive->net_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bills Count</span>
                        <span class="info-box-number">{{ $programDetails->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bill Summary --}}
        <div class="card card-outline card-secondary mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Bill Summary</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row text-center">
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Maintenance</small>
                        <h6 class="mb-0">{{ number_format($billReceive->maintainance, 2) }}</h6>
                    </div>
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Scale Charge</small>
                        <h6 class="mb-0">{{ number_format($billReceive->scale_charge, 2) }}</h6>
                    </div>
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Other Exp</small>
                        <h6 class="mb-0">{{ number_format($billReceive->other_exp, 2) }}</h6>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Other Rcv</small>
                        <h6 class="mb-0">{{ number_format($billReceive->other_rcv, 2) }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== EXCEL-STYLE LEDGER TABLE ===== --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i> Ledger — BSRM Steels Ltd.
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">

                {{-- Export Buttons --}}
            <div class="px-3 pt-3 pb-2">
                <button class="btn btn-sm btn-secondary" id="btn-copy">
                    <i class="fas fa-copy"></i> Copy
                </button>
                <button class="btn btn-sm btn-success" id="btn-csv">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button class="btn btn-sm btn-primary" id="btn-excel">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button class="btn btn-sm btn-danger" id="btn-pdf">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button class="btn btn-sm btn-dark" id="btn-print">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>

                <div class="table-responsive">
                    <table id="ledger-table" class="table table-bordered table-striped table-sm mb-0" style="font-size:12px;">
                        <thead>
                            <tr class="bg-dark text-white text-center">
                                <th style="width:70px">Date</th>
                                <th style="width:110px">Details</th>
                                <th style="width:130px">MV</th>
                                <th style="width:90px">Consignment</th>
                                <th style="width:70px">Bill No.</th>
                                <th>Destination</th>
                                <th style="width:45px">Trip</th>
                                <th style="width:65px">Qty</th>
                                <th style="width:80px">Remarks</th>
                                <th style="width:100px">Dr</th>
                                <th style="width:80px">Cr</th>
                                <th style="width:100px">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $balance  = 0;
                                $totalQty = 0;
                                $totalDr  = 0;
                                $rowNum   = 0;
                            @endphp

                            @foreach ($programDetails as $billNo => $rows)
                                @php
                                    $first   = $rows->first();
                                    $calc    = $billCalculations[$billNo];   // use pre-calculated values

                                    $trip    = $calc['trip'];
                                    $qty     = $calc['dest_qty'];
                                    $dr      = $calc['carrying_bill'];       // ← rate-based calculated amount

                                    $balance  += $dr;
                                    $totalQty += $qty;
                                    $totalDr  += $dr;
                                    $rowNum++;

                                    $mv   = optional($first->motherVassel)->name ?? 'N/A';
                                    $dest = optional($first->destination)->name  ?? 'N/A';
                                    $ghat = optional($first->ghat)->name         ?? 'N/A';
                                @endphp
                                <tr class="text-center {{ $rowNum % 2 == 0 ? 'bg-light' : '' }}">
                                    <td>{{ \Carbon\Carbon::parse($first->date)->format('d-m-y') }}</td>
                                    <td class="text-left">Scrap Carrying Bill</td>
                                    <td class="text-left font-weight-bold" style="color:#1a7a4a;">{{ $mv }}</td>
                                    <td>{{ $first->consignmentno }}</td>
                                    <td><span class="badge badge-primary">{{ $billNo }}</span></td>
                                    <td class="text-left">Scrap carrying from {{ $ghat }} to {{ $dest }}</td>
                                    <td>{{ $trip }}</td>
                                    <td class="text-right">{{ number_format($qty, 2) }}</td>
                                    <td></td>
                                    <td class="text-right font-weight-bold">{{ number_format($dr, 2) }}</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right font-weight-bold text-primary">{{ number_format($balance, 2) }}</td>
                                </tr>
                            @endforeach
                                <tr class="">
                                    <td class="text-center">{{ $billReceive->date }}</td>
                                    <td colspan="4" class="text-right"></td>
                                    <td colspan="4" class="text-left font-weight-bold" style="color:#1a7a4a;">{{ $billReceive->coa->account_name}}</td>
                                    <td class="text-right font-weight-bold">0.00</td>
                                    <td class="text-right font-weight-bold">{{ number_format($billReceive->net_amount , 2) }}</td>
                                    <td class="text-right text-primary font-weight-bold">{{ number_format($balance, 2) }}</td>
                                </tr>
                        </tbody>
                        <tfoot>
                            <tr class="bg-warning font-weight-bold text-center">
                                <td colspan="7" class="text-right">Grand Total</td>
                                <td class="text-right">{{ number_format($totalQty, 2) }}</td>
                                <td></td>
                                <td class="text-right">{{ number_format($totalDr, 2) }}</td>
                                <td class="text-right">{{ number_format($billReceive->net_amount , 2) }}</td>
                                <td class="text-right text-primary">{{ number_format($balance, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Grand Total Boxes --}}
        @php $all = $programDetails->flatten(); @endphp
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calculator mr-1"></i> Grand Total</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h5>{{ $all->sum('dest_qty') }}</h5>
                                <p>Total Dest Qty</p>
                            </div>
                            <div class="icon"><i class="fas fa-weight"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('carrying_bill'), 2) }}</h5>
                                <p>Total Carrying Bill</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('transportcost'), 2) }}</h5>
                                <p>Total Transport Cost</p>
                            </div>
                            <div class="icon"><i class="fas fa-truck"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('advance'), 2) }}</h5>
                                <p>Total Advance</p>
                            </div>
                            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('scale_fee'), 2) }}</h5>
                                <p>Total Scale Fee</p>
                            </div>
                            <div class="icon"><i class="fas fa-balance-scale"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box {{ $all->sum('due') < 0 ? 'bg-danger' : 'bg-success' }}">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('due'), 2) }}</h5>
                                <p>Total Due</p>
                            </div>
                            <div class="icon"><i class="fas fa-balance-scale-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@section('script')
{{-- Required DataTables extension libraries --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(document).ready(function () {

    var table = $('#ledger-table').DataTable({
        responsive: true,
        paging: false,
        searching: true,
        ordering: true,
        info: false,
        autoWidth: false,
        dom: 'Bfrt',   // B = buttons (hidden), f = search, r = processing, t = table
        buttons: [
            {
                extend: 'copyHtml5',
                text: 'Copy',
                title: 'Ledger - BSRM Steels Ltd.',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'csvHtml5',
                text: 'CSV',
                title: 'ledger_{{ $billReceive->date }}',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'excelHtml5',
                text: 'Excel',
                title: 'Ledger - BSRM Steels Ltd.',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'pdfHtml5',
                text: 'PDF',
                title: 'Ledger - BSRM Steels Ltd.',
                orientation: 'landscape',
                pageSize: 'A3',
                exportOptions: { columns: ':visible' }
            },
            {
                extend: 'print',
                text: 'Print',
                title: 'Ledger - BSRM Steels Ltd.',
                exportOptions: { columns: ':visible' }
            }
        ],
        language: {
            search: "",
            searchPlaceholder: "Search ledger..."
        }
    });

    // Hide the DataTables-generated buttons bar (we use our own styled buttons above)
    $('.dt-buttons').hide();

    // Wire our custom buttons to DataTables buttons by index
    $('#btn-copy').on('click', function () {
        table.button(0).trigger();
    });
    $('#btn-csv').on('click', function () {
        table.button(1).trigger();
    });
    $('#btn-excel').on('click', function () {
        table.button(2).trigger();
    });
    $('#btn-pdf').on('click', function () {
        table.button(3).trigger();
    });
    $('#btn-print').on('click', function () {
        table.button(4).trigger();
    });

});
</script>
@endsection