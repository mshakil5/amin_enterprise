@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- ============================================= --}}
        {{-- SUMMARY INFO BOXES --}}
        {{-- ============================================= --}}
        <div class="row mb-3">
            <div class="col-md-2 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-ship"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Programs</span>
                        <span class="info-box-number">{{ $summaries['total_programs'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="fas fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Challans</span>
                        <span class="info-box-number">{{ $summaries['total_challans'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bills Generated</span>
                        <span class="info-box-number">{{ $summaries['bills_generated'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bills Pending</span>
                        <span class="info-box-number">{{ $summaries['bills_pending'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-upload"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">After Posting</span>
                        <span class="info-box-number">{{ $summaries['after_posting'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-danger"><i class="fas fa-trash"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Deleted Records</span>
                        <span class="info-box-number">{{ $summaries['deleted_records'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

                {{-- ============================================= --}}
        {{-- PROGRAM TABLE CARD --}}
        {{-- ============================================= --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i> Program Summary Records
                </h3>
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
                    <table id="programTBL" class="table table-bordered table-striped table-sm mb-0" style="font-size: 12px;">
                        <thead>
                            <tr class="bg-dark text-white text-center">
                                <th style="width:35px">#</th>
                                <th style="width:110px">Date</th>
                                <th>Client</th>
                                <th>Vessels (Mother / Lighter)</th>
                                <th>Ghat</th>
                                <th>Challans</th>
                                <th>Qty</th>
                                <th>Carrying Bill</th>
                                <th>Transport Cost</th>
                                <th>Add. Cost</th>
                                <th>Advance</th>
                                <th>Scale Fee</th>
                                <th>Line Charge</th>
                                <th>Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                            <tr>
                                <td class="text-center align-middle text-muted">{{ $key + 1 }}</td>
                                <td class="text-center align-middle">
                                    {{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}
                                    @if($item->program_detail_min_date && $item->program_detail_max_date)
                                        <br><small class="text-muted">({{ \Carbon\Carbon::parse($item->program_detail_min_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($item->program_detail_max_date)->format('d/m') }})</small>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $item->client->name ?? 'N/A' }}</td>
                                <td class="align-middle">
                                    {{ $item->motherVassel->name ?? 'N/A' }}
                                    @if($item->lighter_vassel_id)
                                        <br><small class="text-muted">LV: {{ $item->lighterVassel->name ?? '' }}</small>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->ghat->name ?? 'N/A' }}</td>
                                <td class="text-center align-middle"><span class="badge badge-info">{{ $item->unique_challan_count ?? 0 }}</span></td>
                                
                                {{-- Sums Columns --}}
                                <td class="text-right align-middle">{{ number_format($item->total_dest_qty ?? 0, 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($item->total_carrying_bill ?? 0, 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($item->total_transportcost ?? 0, 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($item->total_additional_cost ?? 0, 2) }}</td>
                                <td class="text-right align-middle text-primary">{{ number_format($item->total_advance ?? 0, 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($item->total_scale_fee ?? 0, 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($item->total_line_charge ?? 0, 2) }}</td>
                                <td class="text-right align-middle text-danger">{{ number_format($item->total_due ?? 0, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        {{-- ============================================= --}}
                        {{-- TABLE FOOTER (TOTALS) --}}
                        {{-- ============================================= --}}
                        <tfoot>
                            <tr class="bg-light font-weight-bold text-dark">
                                <td colspan="6" class="text-right align-middle">TOTAL</td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_dest_qty'), 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_carrying_bill'), 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_transportcost'), 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_additional_cost'), 2) }}</td>
                                <td class="text-right align-middle text-primary">{{ number_format($data->sum('total_advance'), 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_scale_fee'), 2) }}</td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_line_charge'), 2) }}</td>
                                <td class="text-right align-middle text-danger">{{ number_format($data->sum('total_due'), 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>



    </div>
</section>


@endsection

{{-- ============================================= --}}
{{-- STYLES (matching Income module) --}}
{{-- ============================================= --}}
@section('style')
<style>
    .info-box .info-box-number {
        font-size: 16px !important;
    }
    .btn-action {
        padding: 3px 8px;
        font-size: 11px;
        margin-right: 2px;
    }
    .badge {
        font-size: 10px;
        padding: 4px 7px;
    }
    /* Modal info-box compact */
    .modal .info-box {
        margin-bottom: 10px;
    }
    .modal .info-box .info-box-icon {
        width: 55px;
        font-size: 16px;
    }
    .modal .info-box .info-box-content {
        padding: 5px 10px;
    }
    .modal .info-box .info-box-number {
        font-size: 18px !important;
    }
    .modal .info-box .info-box-text {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endsection

{{-- ============================================= --}}
{{-- SCRIPTS (matching Income module structure) --}}
{{-- ============================================= --}}
@section('script')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
 $(document).ready(function() {

    // =============================================
    // DATATABLE INITIALIZATION
    // =============================================
    var programTBL = $('#programTBL').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        pageLength: 50, // Sets default to 50
        lengthMenu: [[50, 100, 200, -1], [50, 100, 200, "All"]], 
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy',  className: 'btn btn-sm btn-secondary', text: '<i class="fas fa-copy"></i> Copy' },
            { extend: 'csv',   className: 'btn btn-sm btn-success',   text: '<i class="fas fa-file-csv"></i> CSV' },
            { extend: 'excel', className: 'btn btn-sm btn-primary',   text: '<i class="fas fa-file-excel"></i> Excel' },
            { extend: 'pdf',   className: 'btn btn-sm btn-danger',    text: '<i class="fas fa-file-pdf"></i> PDF' },
            { extend: 'print', className: 'btn btn-sm btn-dark',      text: '<i class="fas fa-print"></i> Print' }
        ],
        columnDefs: [
            { orderable: false, targets: [0] } // Disable sorting on the first column (#)
        ],
        order: [],
        language: {
            search: "",
            searchPlaceholder: "Search programs...",
            emptyTable: "No program records found",
            zeroRecords: "No matching records found"
        }
    });

    // Hide default export buttons (we use custom ones above the table)
    $('.dt-buttons').hide();

    // Bind custom export buttons
    $('#btn-copy').on('click',  function() { programTBL.button(0).trigger(); });
    $('#btn-csv').on('click',   function() { programTBL.button(1).trigger(); });
    $('#btn-excel').on('click', function() { programTBL.button(2).trigger(); });
    $('#btn-pdf').on('click',   function() { programTBL.button(3).trigger(); });
    $('#btn-print').on('click', function() { programTBL.button(4).trigger(); });


});
</script>
@endsection