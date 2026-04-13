@extends('admin.layouts.admin')

@section('content')

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Bill Receivables</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Bill Receivables</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <!-- Toast Container -->
                <div class="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $billReceive->count() }}</h3>
                                <p>Total Bills</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($billReceive->sum('net_amount'), 2) }}</h3>
                                <p>Total Amount</p>
                            </div>
                            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($billReceive->sum('qty'), 2) }}</h3>
                                <p>Total Quantity</p>
                            </div>
                            <div class="icon"><i class="fas fa-weight-hanging"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ $billReceive->where('rcv_type', 'Bank')->count() }}</h3>
                                <p>Bank Receives</p>
                            </div>
                            <div class="icon"><i class="fas fa-university"></i></div>
                        </div>
                    </div>
                </div>

                <!-- ===================== TABS ===================== -->
                <div class="card card-primary card-outline">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="receivableTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="tab-list-link" data-toggle="pill"
                                   href="#tab-list" role="tab">
                                    <i class="fas fa-list mr-1"></i> Receivables List
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="tab-ledger-link" data-toggle="pill"
                                   href="#tab-ledger" role="tab">
                                    <i class="fas fa-table mr-1"></i> All Details Ledger
                                    <span class="badge badge-primary ml-1">{{ $programDetails->count() }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <div class="tab-content" id="receivableTabsContent">

                            {{-- ======= TAB 1: RECEIVABLES LIST ======= --}}
                            <div class="tab-pane fade show active" id="tab-list" role="tabpanel">

                                @if($billReceive->isEmpty())
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-2"></i>No receivables found.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table id="billReceiveTable" class="table table-bordered table-striped table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th width="50">SL</th>
                                                    <th width="100">Date</th>
                                                    <th width="100">Client</th>
                                                    <th width="250">Bill List</th>
                                                    <th width="110">Receive Type</th>
                                                    <th class="text-right" width="80">Qty</th>
                                                    <th class="text-right" width="110">Total Amount</th>
                                                    <th class="text-right" width="100">Maintenance</th>
                                                    <th class="text-right" width="100">Scale Charge</th>
                                                    <th class="text-right" width="90">Other Exp</th>
                                                    <th class="text-right" width="90">Other Rcv</th>
                                                    <th class="text-right" width="110">Net Amount</th>
                                                    <th width="130">Status</th>
                                                    <th width="70">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($billReceive as $index => $bill)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ date('d-m-Y', strtotime($bill->date)) }}</td>
                                                        <td class="text-center">{{ $bill->client->name ?? '' }}</td>
                                                        <td>
                                                            @if($bill->bill_list)
                                                                @php
                                                                    $bills        = explode(',', $bill->bill_list);
                                                                    $displayLimit = 3;
                                                                    $displayBills = array_slice($bills, 0, $displayLimit);
                                                                    $remainingCount = count($bills) - $displayLimit;
                                                                    $hiddenBills  = implode(', ', array_slice($bills, $displayLimit));
                                                                @endphp
                                                                <div class="d-flex flex-wrap" style="gap:4px;">
                                                                    @foreach($displayBills as $b)
                                                                        <span class="badge badge-secondary text-sm">{{ trim($b) }}</span>
                                                                    @endforeach
                                                                    @if($remainingCount > 0)
                                                                        <span class="badge badge-primary text-sm"
                                                                              data-toggle="tooltip" data-html="true"
                                                                              data-placement="top"
                                                                              title="{{ htmlspecialchars($hiddenBills) }}">
                                                                            +{{ $remainingCount }} more
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($bill->rcv_type == 'Bank')
                                                                <span class="badge badge-primary"><i class="fas fa-university mr-1"></i>Bank</span>
                                                            @elseif($bill->rcv_type == 'Cash')
                                                                <span class="badge badge-success"><i class="fas fa-money-bill mr-1"></i>Cash</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ $bill->rcv_type }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">{{ number_format($bill->qty, 2) }}</td>
                                                        <td class="text-right">{{ number_format($bill->total_amount, 2) }}</td>
                                                        <td class="text-right">{{ number_format($bill->maintainance, 2) }}</td>
                                                        <td class="text-right">{{ number_format($bill->scale_charge, 2) }}</td>
                                                        <td class="text-right">{{ number_format($bill->other_exp, 2) }}</td>
                                                        <td class="text-right">{{ number_format($bill->other_rcv, 2) }}</td>
                                                        <td class="text-right"><strong>{{ number_format($bill->net_amount, 2) }}</strong></td>
                                                        <td>
                                                            @if($bill->receive_status == 1)
                                                                <button type="button" class="btn btn-xs btn-success receive-toggle"
                                                                        data-id="{{ $bill->id }}" data-status="0"
                                                                        title="Click to mark as Not Received">
                                                                    <i class="fas fa-check-circle mr-1"></i>Received
                                                                </button>
                                                            @else
                                                                <button type="button" class="btn btn-xs btn-danger receive-toggle"
                                                                        data-id="{{ $bill->id }}" data-status="1"
                                                                        title="Click to mark as Received">
                                                                    <i class="fas fa-times-circle mr-1"></i>Not Received
                                                                </button>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <button type="button" class="btn btn-danger delete-btn"
                                                                        data-id="{{ $bill->id }}" title="Delete">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                                <a href="{{ route('admin.getReceivablesDetails', $bill->id) }}"
                                                                   class="btn btn-success" title="Details">Details</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="font-weight-bold bg-light">
                                                    <td colspan="5" class="text-right">Total:</td>
                                                    <td class="text-right">{{ number_format($billReceive->sum('qty'), 2) }}</td>
                                                    <td class="text-right">{{ number_format($billReceive->sum('total_amount'), 2) }}</td>
                                                    <td class="text-right">{{ number_format($billReceive->sum('maintainance'), 2) }}</td>
                                                    <td class="text-right">{{ number_format($billReceive->sum('scale_charge'), 2) }}</td>
                                                    <td class="text-right">{{ number_format($billReceive->sum('other_exp'), 2) }}</td>
                                                    <td class="text-right">{{ number_format($billReceive->sum('other_rcv'), 2) }}</td>
                                                    <td class="text-right">{{ number_format($billReceive->sum('net_amount'), 2) }}</td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @endif

                            </div>{{-- end tab-list --}}


                            {{-- ======= TAB 2: ALL DETAILS LEDGER ======= --}}
                            <div class="tab-pane fade" id="tab-ledger" role="tabpanel">

                                @php
                                    $billNoToReceiveId = [];
                                    foreach($billReceive as $br) {
                                        if($br->bill_list) {
                                            foreach(array_map('trim', explode(',', $br->bill_list)) as $bn) {
                                                $billNoToReceiveId[$bn] = $br->id;
                                            }
                                        }
                                    }
                                @endphp

                                @if($programDetails->isEmpty())
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-2"></i>No detail records found.
                                    </div>
                                @else
                                    {{-- Export Buttons --}}
                                    <div class="mb-2">
                                        <button class="btn btn-sm btn-secondary" id="btn-copy-all"><i class="fas fa-copy"></i> Copy</button>
                                        <button class="btn btn-sm btn-success"   id="btn-csv-all"><i class="fas fa-file-csv"></i> CSV</button>
                                        <button class="btn btn-sm btn-primary"   id="btn-excel-all"><i class="fas fa-file-excel"></i> Excel</button>
                                        <button class="btn btn-sm btn-danger"    id="btn-pdf-all"><i class="fas fa-file-pdf"></i> PDF</button>
                                        <button class="btn btn-sm btn-dark"      id="btn-print-all"><i class="fas fa-print"></i> Print</button>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="allLedgerTable" class="table table-bordered table-striped table-sm" style="font-size:12px;">
                                            <thead>
                                                <tr class="bg-dark text-white text-center">
                                                    <th style="width:10px">Action</th>
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
                                                    $balance2  = 0;
                                                    $totalQty2 = 0;
                                                    $totalDr2  = 0;
                                                    $totalCr2  = 0;
                                                    $rowNum2   = 0;
                                                @endphp

                                                @foreach ($programDetails as $billNo => $rows)
                                                    @php
                                                        $first2   = $rows->first();
                                                        $calc2    = $billCalculations[$billNo];
                                                        $trip2    = $calc2['trip'];
                                                        $qty2     = $calc2['dest_qty'];
                                                        $dr2      = $calc2['carrying_bill'];
                                                        $balance2 += $dr2;
                                                        $totalQty2 += $qty2;
                                                        $totalDr2  += $dr2;
                                                        $rowNum2++;

                                                        $mv2   = optional($first2->motherVassel)->name ?? 'N/A';
                                                        $dest2 = optional($first2->destination)->name  ?? 'N/A';
                                                        $ghat2 = optional($first2->ghat)->name         ?? 'N/A';

                                                        $hasCheque2  = isset($billChequeMap[$billNo]);
                                                        $chequeInfo2 = $hasCheque2 ? $billChequeMap[$billNo] : null;
                                                        $brId        = $billNoToReceiveId[$billNo] ?? '';
                                                    @endphp
                                                    <tr class="text-center all-ledger-row {{ $rowNum2 % 2 == 0 ? 'bg-light' : '' }} {{ $hasCheque2 ? 'cheque-applied-row' : '' }}"
                                                        data-billno="{{ $billNo }}"
                                                        data-billreceiveid="{{ $brId }}"
                                                        data-dr="{{ $dr2 }}">
                                                        <td>
                                                            <input type="checkbox"
                                                                class="form-check-input all-bill-checkbox"
                                                                data-billno="{{ $billNo }}"
                                                                data-billreceiveid="{{ $brId }}"
                                                                {{ $hasCheque2 ? 'checked disabled' : '' }}>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($first2->date)->format('d-m-y') }}</td>
                                                        <td class="text-left">Scrap Carrying Bill</td>
                                                        <td class="text-left font-weight-bold" style="color:#1a7a4a;">{{ $mv2 }}</td>
                                                        <td>{{ $first2->consignmentno }}</td>
                                                        <td><span class="badge badge-primary">{{ $billNo }}</span></td>
                                                        <td class="text-left">Scrap carrying from {{ $ghat2 }} to {{ $dest2 }}</td>
                                                        <td>{{ $trip2 }}</td>
                                                        <td class="text-right">{{ number_format($qty2, 2) }}</td>
                                                        <td class="text-center">
                                                            @if($hasCheque2)
                                                                <small class="badge badge-success" style="font-size:9px;"
                                                                    title="Chq: {{ $chequeInfo2->cheque_number }} | Bank: {{ $chequeInfo2->bank_name ?? 'N/A' }}">
                                                                    <i class="fas fa-money-check-alt"></i> Chq
                                                                </small>
                                                            @endif
                                                        </td>
                                                        <td class="text-right font-weight-bold">{{ number_format($dr2, 2) }}</td>
                                                        <td class="text-right">-</td>
                                                        <td class="text-right font-weight-bold text-primary">{{ number_format($balance2, 2) }}</td>
                                                    </tr>
                                                @endforeach

                                                {{-- Cheque Credit Rows --}}
                                                @foreach($chequeDetails as $cheque2)
                                                    @php
                                                        $balance2 -= (float) $cheque2->cheque_amount;
                                                        $totalCr2 += (float) $cheque2->cheque_amount;
                                                        $chBills2  = json_decode($cheque2->bill_nos, true) ?? [];
                                                    @endphp
                                                    <tr class="text-center" style="background-color:#e8f5e9;">
                                                        <td>
                                                            <button type="button" class="btn btn-link p-0 text-danger all-view-cheque-btn"
                                                                    data-id="{{ $cheque2->id }}" title="View Cheque" style="font-size:11px;">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($cheque2->cheque_date)->format('d-m-y') }}</td>
                                                        <td class="text-left font-weight-bold text-success">Cheque Payment</td>
                                                        <td colspan="2" class="text-left">
                                                            <small style="font-size:10px;">
                                                                Chq: <strong>{{ $cheque2->cheque_number }}</strong>
                                                                @if($cheque2->bank_name) | {{ $cheque2->bank_name }} @endif
                                                            </small>
                                                        </td>
                                                        <td><small class="text-muted" style="font-size:9px;">{{ count($chBills2) }} bill(s)</small></td>
                                                        <td class="text-left" colspan="2">
                                                            <small class="text-muted" style="font-size:9px;">{{ implode(', ', $chBills2) }}</small>
                                                        </td>
                                                        <td></td>
                                                        <td>
                                                            @if($cheque2->document_path)
                                                                <a href="{{ asset($cheque2->document_path) }}" target="_blank"
                                                                class="btn btn-link p-0" style="font-size:10px;">
                                                                    <i class="fas fa-paperclip text-primary"></i> Doc
                                                                </a>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">-</td>
                                                        <td class="text-right font-weight-bold text-danger">{{ number_format($cheque2->cheque_amount, 2) }}</td>
                                                        <td class="text-right font-weight-bold text-primary">{{ number_format($balance2, 2) }}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-warning font-weight-bold text-center">
                                                    <td colspan="8" class="text-right">Grand Total</td>
                                                    <td class="text-right">{{ number_format($totalQty2, 2) }}</td>
                                                    <td></td>
                                                    <td class="text-right">{{ number_format($totalDr2, 2) }}</td>
                                                    <td class="text-right text-danger">{{ number_format($totalCr2, 2) }}</td>
                                                    <td class="text-right text-primary">{{ number_format($balance2, 2) }}</td>
                                                </tr>
                                                <tr class="font-weight-bold text-center">
                                                    <td colspan="11" class="text-right">
                                                        <span id="all-selected-count" class="text-muted" style="font-size:11px;"></span>
                                                    </td>
                                                    <td colspan="2" class="text-center">
                                                        <button id="btn-add-cheque-all" class="btn btn-danger btn-sm" disabled>
                                                            <i class="fas fa-money-check-alt mr-1"></i> Add Cheque Number
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>



                                @endif

                            </div>{{-- end tab-ledger --}}

                        </div>{{-- end tab-content --}}
                    </div>{{-- end card-body --}}
                </div>{{-- end card --}}

            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this bill receive?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
$(function() {

    // ── Tab 1: Receivables DataTable ──────────────────────────────
    var table1 = $('#billReceiveTable').DataTable({
        responsive: true,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        pageLength: 25,
        order: [[0, 'desc']],
        columnDefs: [{ orderable: false, targets: -1 }],
        autoWidth: false
    });

    $('[data-toggle="tooltip"]').tooltip({ container: 'body', boundary: 'window' });

    // ── Tab 2: All Ledger DataTable ───────────────────────────────
    var table2 = $('#allLedgerTable').DataTable({
        responsive: true,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
        pageLength: 50,
        order: [[0, 'asc']],
        columnDefs: [{ orderable: false, targets: [0, 9] }],
        autoWidth: false,
        dom: 'lrtip',  // hide default search; we use custom buttons
        buttons: [
            { extend: 'copy',       text: '<i class="fas fa-copy"></i> Copy' },
            { extend: 'csv',        text: '<i class="fas fa-file-csv"></i> CSV' },
            { extend: 'excel',      text: '<i class="fas fa-file-excel"></i> Excel', title: 'All Receivable Details' },
            { extend: 'pdf',        text: '<i class="fas fa-file-pdf"></i> PDF',   title: 'All Receivable Details' },
            { extend: 'print',      text: '<i class="fas fa-print"></i> Print' }
        ]
    });

    // Wire up custom export buttons for Tab 2
    $('#btn-copy-all').on('click',  function() { table2.button('.buttons-copy').trigger(); });
    $('#btn-csv-all').on('click',   function() { table2.button('.buttons-csv').trigger(); });
    $('#btn-excel-all').on('click', function() { table2.button('.buttons-excel').trigger(); });
    $('#btn-pdf-all').on('click',   function() { table2.button('.buttons-pdf').trigger(); });
    $('#btn-print-all').on('click', function() { table2.button('.buttons-print').trigger(); });

    // Re-draw Tab 2 table when tab becomes visible (fixes column widths)
    $('a[href="#tab-ledger"]').on('shown.bs.tab', function() {
        table2.columns.adjust().draw();
    });

    // ── Delete ────────────────────────────────────────────────────
    $('.delete-btn').on('click', function() {
        var id = $(this).data('id');
        var deleteUrl = '{{ route("admin.bill-receives.destroy", ":id") }}'.replace(':id', id);
        $('#deleteForm').attr('action', deleteUrl);
        $('#deleteModal').modal('show');
    });

    // ── Receive Status Toggle ─────────────────────────────────────
    $(document).on('click', '.receive-toggle', function() {
        var btn    = $(this);
        var id     = btn.data('id');
        var status = btn.data('status');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Updating...');

        $.ajax({
            url: '/admin/bill-receives/update-receive-status',
            method: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), id: id, receive_status: status },
            success: function(response) {
                if (response.status == 1) {
                    btn.removeClass('btn-danger').addClass('btn-success').data('status', '0')
                       .html('<i class="fas fa-check-circle mr-1"></i>Received')
                       .attr('title', 'Click to mark as Not Received');
                    showToast('Marked as Received', 'success');
                } else {
                    btn.removeClass('btn-success').addClass('btn-danger').data('status', '1')
                       .html('<i class="fas fa-times-circle mr-1"></i>Not Received')
                       .attr('title', 'Click to mark as Received');
                    showToast('Marked as Not Received', 'warning');
                }
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Failed to update status.', 'error');
            },
            complete: function() { btn.prop('disabled', false); }
        });
    });

    // ── Toast ─────────────────────────────────────────────────────
    function showToast(message, type) {
        var bgColor   = type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning' : 'bg-danger';
        var textColor = type === 'warning' ? 'text-dark' : 'text-white';
        var icon      = type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-circle' : 'fa-times-circle';

        var toast = $(`
            <div class="toast" role="alert" data-delay="3000" style="min-width:300px;">
                <div class="toast-body ${bgColor} ${textColor} d-flex align-items-center px-3 py-2">
                    <i class="fas ${icon} mr-2"></i>${message}
                    <button type="button" class="ml-auto ${textColor}" data-dismiss="toast"
                            style="background:none;border:none;font-size:1.2rem;">&times;</button>
                </div>
            </div>`);

        $('.toast-container').append(toast);
        toast.toast('show');
        toast.on('hidden.bs.toast', function() { toast.remove(); });
    }

});


// ============================================================
// TAB 2 — All Ledger Cheque Logic
// ============================================================
$(function() {

    let allSelectedBillNos    = [];
    let allCurrentViewChequeId = null;

    // Checkbox toggle
    $('#allLedgerTable').on('change', '.all-bill-checkbox:not(:disabled)', function() {
        const billNo = $(this).data('billno');
        if ($(this).is(':checked')) {
            if (!allSelectedBillNos.includes(billNo)) allSelectedBillNos.push(billNo);
        } else {
            allSelectedBillNos = allSelectedBillNos.filter(b => b !== billNo);
        }
        updateAllSelectedCount();
        updateAllButtonState();
    });

    // View cheque eye button
    $('#allLedgerTable').on('click', '.all-view-cheque-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const chequeId = $(this).data('id');
        allCurrentViewChequeId = chequeId;

        $.ajax({
            url: '{{ route("cheque.view") }}',
            type: 'POST',
            data: { cheque_id: chequeId, _token: '{{ csrf_token() }}' },
            success: function(response) {
                const c = response.cheque;
                let html = '<table class="table table-sm table-bordered mb-0">';
                html += '<tr><th style="width:35%">Cheque Number</th><td><strong>' + c.cheque_number + '</strong></td></tr>';
                html += '<tr><th>Cheque Date</th><td>' + c.cheque_date + '</td></tr>';
                html += '<tr><th>Bank Name</th><td>' + (c.bank_name || 'N/A') + '</td></tr>';
                html += '<tr><th>Amount</th><td class="font-weight-bold text-danger">' + parseFloat(c.cheque_amount).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</td></tr>';
                html += '<tr><th>Bills</th><td>' + c.bill_nos.join(', ') + '</td></tr>';
                if (c.document_path) {
                    html += '<tr><th>Document</th><td><a href="' + c.document_path + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download mr-1"></i>' + (c.document_name || 'View Document') + '</a></td></tr>';
                }
                html += '<tr><th>Created At</th><td>' + c.created_at + '</td></tr>';
                html += '</table>';
                $('#all-cheque-view-body').html(html);
                $('#allChequeViewModal').modal('show');
            },
            error: function() { showAllToast('Error loading cheque details', 'error'); }
        });
    });

    // Add Cheque button (tfoot is outside DataTables body so bind on tfoot)
    $(document).on('click', '#btn-add-cheque-all', function() {
        if (allSelectedBillNos.length === 0) {
            showAllToast('Please select at least one bill', 'warning');
            return;
        }
        allCheckExistingCheque(allSelectedBillNos);
    });

    // Delete cheque from view modal
    $('#allChequeViewModal').on('click', '#all-btn-delete-cheque', function() {
        if (!allCurrentViewChequeId) return;
        if (!confirm('Are you sure you want to delete this cheque entry?')) return;

        $.ajax({
            url: '{{ route("cheque.delete") }}',
            type: 'POST',
            data: { cheque_id: allCurrentViewChequeId, _token: '{{ csrf_token() }}', _method: 'DELETE' },
            success: function(response) {
                showAllToast(response.message || 'Cheque deleted successfully!', 'success');
                $('#allChequeViewModal').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            },
            error: function(xhr) {
                showAllToast(xhr.responseJSON?.message || 'Error deleting cheque', 'error');
            }
        });
    });

    function updateAllSelectedCount() {
        const count = allSelectedBillNos.length;
        $('#all-selected-count').html(
            count > 0 ? '<span class="badge badge-info">' + count + ' bill(s) selected</span>' : ''
        );
    }

    function updateAllButtonState() {
        const btn = $('#btn-add-cheque-all');
        if (allSelectedBillNos.length > 0) {
            btn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-danger');
        } else {
            btn.prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
        }
    }

    function allCheckExistingCheque(billNos) {
        // Get bill_receive_id from the first selected row
        const firstRow = $('tr.all-ledger-row[data-billno="' + billNos[0] + '"]');
        const billReceiveId = firstRow.data('billreceiveid') || '';

        $.ajax({
            url: '{{ route("cheque.check-existing") }}',
            type: 'POST',
            data: { bill_nos: billNos, _token: '{{ csrf_token() }}' },
            success: function(response) {
                allResetForm();
                $('#all-selected-bills-display').text(billNos.join(', '));
                $('#all_bill_nos').val(JSON.stringify(billNos));
                $('#all_bill_receive_id').val(billReceiveId);

                if (response.exists && response.cheque) {
                    $('#all_cheque_id').val(response.cheque.id);
                    $('#all_cheque_number').val(response.cheque.cheque_number);
                    $('#all_cheque_date').val(response.cheque.cheque_date);
                    $('#all_bank_name').val(response.cheque.bank_name);
                    $('#all_cheque_amount').val(response.cheque.cheque_amount);
                    $('#all-existing-cheque-info').removeClass('d-none');
                    $('#all-btn-save-cheque').html('<i class="fas fa-edit mr-1"></i>Update Cheque Details');
                    if (response.cheque.document_path) {
                        allShowExistingDocument(response.cheque.document_path, response.cheque.document_name);
                    }
                } else {
                    $('#all-btn-save-cheque').html('<i class="fas fa-save mr-1"></i>Save Cheque Details');
                    // Auto-sum Dr values of selected bills
                    let totalAmount = 0;
                    billNos.forEach(function(billNo) {
                        const row = $('tr.all-ledger-row[data-billno="' + billNo + '"]');
                        totalAmount += parseFloat(row.data('dr')) || 0;
                    });
                    $('#all_cheque_amount').val(totalAmount.toFixed(2));
                }

                $('#allChequeModal').modal('show');
            },
            error: function() { showAllToast('Error checking existing cheque data', 'error'); }
        });
    }

    // Document preview
    $('#all_cheque_document').on('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) {
            showAllToast('File size must be less than 5MB', 'warning');
            $(this).val('');
            return;
        }
        $(this).next('.custom-file-label').text(file.name);
        const reader = new FileReader();
        reader.onload = function(event) {
            const ext = file.name.split('.').pop().toLowerCase();
            if (['jpg','jpeg','png','gif'].includes(ext)) {
                $('#all-preview-image').attr('src', event.target.result).show();
                $('#all-preview-file').addClass('d-none');
            } else {
                $('#all-preview-image').hide();
                $('#all-preview-file').removeClass('d-none');
                $('#all-file-name').text(file.name);
            }
            $('#all-document-preview').removeClass('d-none');
        };
        reader.readAsDataURL(file);
    });

    $('#all-remove-document').on('click', function() {
        $('#all_cheque_document').val('');
        $('#all_cheque_document').next('.custom-file-label').text('Choose file (PDF, JPG, PNG, DOC)');
        $('#all-document-preview').addClass('d-none');
        $('#all-preview-image').attr('src', '').hide();
        $('#all-preview-file').addClass('d-none');
    });

    function allShowExistingDocument(path, fileName) {
        const ext = path.split('.').pop().toLowerCase();
        if (['jpg','jpeg','png','gif'].includes(ext)) {
            $('#all-preview-image').attr('src', path).show();
            $('#all-preview-file').addClass('d-none');
        } else {
            $('#all-preview-image').hide();
            $('#all-preview-file').removeClass('d-none');
            $('#all-file-name').text(fileName || path.split('/').pop());
        }
        $('#all-document-preview').removeClass('d-none');
    }

    function allResetForm() {
        $('#allChequeForm')[0].reset();
        $('#all_cheque_id').val('');
        $('#all-existing-cheque-info').addClass('d-none');
        $('#all-document-preview').addClass('d-none');
        $('#all-preview-image').hide();
        $('#all-preview-file').addClass('d-none');
        $('#all_cheque_document').next('.custom-file-label').text('Choose file (PDF, JPG, PNG, DOC)');
    }

    // Form submit
    $('#allChequeForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const billNos  = JSON.parse($('#all_bill_nos').val());
        formData.append('_token', '{{ csrf_token() }}');
        formData.set('bill_nos', billNos.join(','));

        const chequeId = $('#all_cheque_id').val();
        if (chequeId) {
            formData.append('cheque_id', chequeId);
            formData.append('_method', 'PUT');
        }

        const btn = $('#all-btn-save-cheque');
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: chequeId
                ? '{{ route("cheque.update", ":id") }}'.replace(':id', chequeId)
                : '{{ route("cheque.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showAllToast(response.message || 'Cheque details saved successfully!', 'success');
                $('#allChequeModal').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            },
            error: function(xhr) {
                if (xhr.status === 419) { showAllToast('Session expired. Please refresh.', 'error'); return; }
                let errors = xhr.responseJSON?.message || 'Error saving cheque details';
                if (xhr.responseJSON?.errors) {
                    errors = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                showAllToast(errors, 'error');
            },
            complete: function() { btn.html(originalText).prop('disabled', false); }
        });
    });

    function showAllToast(message, type) {
        type = type || 'info';
        const icons = { success:'fas fa-check-circle', error:'fas fa-exclamation-circle', warning:'fas fa-exclamation-triangle', info:'fas fa-info-circle' };
        const bg    = { success:'bg-success', error:'bg-danger', warning:'bg-warning', info:'bg-info' };
        const t = '<div class="toast-container position-fixed top-0 right-0 p-3" style="z-index:9999;"><div class="toast show ' + bg[type] + ' text-white" role="alert" style="min-width:300px;"><div class="toast-header ' + bg[type] + ' text-white border-0" style="font-size:12px;"><i class="' + icons[type] + ' mr-2"></i><strong class="mr-auto">' + type.charAt(0).toUpperCase() + type.slice(1) + '</strong><button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button></div><div class="toast-body" style="font-size:12px;">' + message + '</div></div></div>';
        $('body').append(t);
        setTimeout(function() { $('.toast-container').remove(); }, 4000);
    }

});


</script>
@endsection