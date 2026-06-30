@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- ============================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ============================================= --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-ship mr-2 text-primary"></i>Program Management
            </h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.addProgram') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Before Challan Receive
                </a>
                <a href="{{ route('admin.afterPostProgram') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-right mr-1"></i> After Challan Receive
                </a>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- SUMMARY INFO BOXES --}}
        {{-- ============================================= --}}
        <div class="row mb-3">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-ship"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Programs</span>
                        <span class="info-box-number">{{ $summaries['total_programs'] }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Challans</span>
                        <span class="info-box-number">{{ number_format($summaries['total_challans']) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bills Generated</span>
                        <span class="info-box-number">{{ number_format($summaries['bills_generated']) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bills Pending</span>
                        <span class="info-box-number">{{ number_format($summaries['bills_pending']) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-exchange-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">After Posting</span>
                        <span class="info-box-number">{{ number_format($summaries['after_posting']) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-trash-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Deleted Records</span>
                        <span class="info-box-number">{{ number_format($summaries['deleted_records']) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- FILTER CARD --}}
        {{-- ============================================= --}}
        <div class="card card-outline card-secondary mb-4">
            <div class="card-header py-2">
                <h3 class="card-title text-sm">
                    <i class="fas fa-filter mr-1"></i> Filter Options
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body py-3">
                <form class="form-inline" id="filter-form" method="GET" action="{{ route('admin.allProgram') }}">
                    <div class="form-group mx-sm-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            </div>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                        </div>
                    </div>
                    <div class="form-group mx-sm-2">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            </div>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="form-group mx-sm-2">
                        <select class="form-control form-control-sm select2" name="client_id" style="width: 200px;">
                            <option value="">All Clients</option>
                            @foreach($clients as $id => $name)
                                <option value="{{ $id }}" {{ request('client_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mx-sm-2">
                        <select class="form-control form-control-sm select2" name="mother_vassel_id" style="width: 200px;">
                            <option value="">All Vessels</option>
                            @foreach($vessels as $id => $name)
                                <option value="{{ $id }}" {{ request('mother_vassel_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i>Search
                    </button>
                    <a href="{{ route('admin.allProgram') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-redo mr-1"></i>Reset
                    </a>
                </form>
            </div>
        </div>

        {{-- ============================================= --}}
        {{-- PROGRAM TABLE CARD --}}
        {{-- ============================================= --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i> Program Records
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
                                <th style="width:85px">Date</th>
                                <th style="width:130px">Client</th>
                                <th style="width:100px">Program ID</th>
                                <th style="width:140px">Vessels</th>
                                <th style="width:90px">Ghat</th>
                                <th style="width:110px">Consignment</th>
                                <th style="width:90px">Log</th>
                                <th style="width:140px">Billing</th>
                                <th style="width:100px">Vendor</th>
                                <th style="width:130px">Challans</th>
                                <th style="width:90px">Deleted</th>
                                <th style="width:70px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                            <tr>
                                <td class="text-center align-middle text-muted">{{ $key + 1 }}</td>
                                <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                <td class="align-middle">{{ $item->client->name ?? 'N/A' }}</td>
                                <td class="text-center align-middle font-weight-bold">{{ $item->programid }}</td>
                                <td class="align-middle">
                                    {{ $item->motherVassel->name ?? '' }}
                                    @if($item->lighter_vassel_id)
                                        <br><small class="text-muted">LV: {{ $item->lighterVassel->name ?? '' }}</small>
                                    @endif
                                </td>
                                <td class="text-center align-middle">{{ $item->ghat->name ?? 'N/A' }}</td>
                                <td class="text-center align-middle">{{ $item->consignmentno ?? 'N/A' }}</td>

                                {{-- LOG --}}
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-dark btn-action" data-toggle="modal" data-target="#logModal_{{ $item->id }}" title="View Log">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(in_array('13', json_decode(auth()->user()->role->permission)))
                                    <a href="{{ route('admin.getTruckListByVendor', $item->id) }}" class="btn btn-info btn-action d-block mt-1" title="Truck List">
                                        <i class="fas fa-truck"></i>
                                    </a>
                                    @endif
                                </td>

                                {{-- BILLING --}}
                                <td class="text-center align-middle">
                                    @if(in_array('13', json_decode(auth()->user()->role->permission)))
                                    <a href="{{ route('billGenerating', $item->id) }}" class="btn btn-success btn-action d-block mb-1" title="Generate Bill">
                                        <i class="fas fa-file-invoice-dollar mr-1"></i>Gen Bill
                                    </a>
                                    @endif

                                    <div class="d-flex justify-content-center gap-1 flex-wrap mb-1">
                                        @if ($item->generate_bill_count > 0)
                                            <a href="{{ route('bill.generated', $item->id) }}" class="badge badge-success" title="Generated">{{ $item->generate_bill_count }} Gen</a>
                                        @endif
                                        @if ($item->not_generate_bill_count > 0)
                                            <a href="{{ route('bill.not.generated', $item->id) }}" class="badge badge-danger" title="Pending">{{ $item->not_generate_bill_count }} Pen</a>
                                        @endif
                                    </div>

                                    @if ($item->bill_status == 1)
                                        <a href="{{ route('generatingBillShow', $item->id) }}" class="btn btn-outline-secondary btn-action d-block" title="View Bill">View Bill</a>
                                    @endif
                                </td>

                                {{-- VENDOR --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('admin.programVendorList', $item->id) }}" class="btn btn-info btn-action d-block mb-1">Vendor</a>
                                    <a href="{{ route('admin.programVendorDocuments', $item->id) }}" class="btn btn-outline-dark btn-action d-block">Docs</a>
                                </td>

                                {{-- CHALLANS --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('admin.programDetail', $item->id) }}" class="btn btn-primary btn-action d-block mb-1">
                                        Total: {{ $item->unique_challan_count }}
                                    </a>
                                    @if ($item->qty_change == 1)
                                        <span class="badge badge-warning">12 MT</span>
                                    @else
                                        <span class="badge badge-secondary">Actual</span>
                                    @endif
                                </td>

                                {{-- DELETED --}}
                                <td class="text-center align-middle">
                                    @if ($item->deleted_count > 0)
                                        <a href="{{ route('admin.deletedProgramDetail', $item->id) }}" class="btn btn-danger btn-action">
                                            <i class="fas fa-trash mr-1"></i>{{ $item->deleted_count }}
                                        </a>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('admin.programEdit', $item->id) }}" class="btn btn-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ============================================= --}}
{{-- LOG MODALS (outside table for cleaner DOM) --}}
{{-- ============================================= --}}
@foreach ($data as $item)
<div class="modal fade" id="logModal_{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clipboard-list mr-2"></i>Log Details — {{ $item->programid }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                {{-- Basic Info Table --}}
                <table class="table table-sm table-borderless mb-0" style="font-size: 13px;">
                    <tbody>
                        <tr class="bg-light">
                            <td class="font-weight-bold text-muted" style="width:35%">Mother Vessel</td>
                            <td>{{ $item->motherVassel->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-muted">Details Start Date</td>
                            <td>{{ $item->program_detail_min_date ? \Carbon\Carbon::parse($item->program_detail_min_date)->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr class="bg-light">
                            <td class="font-weight-bold text-muted">Details End Date</td>
                            <td>{{ $item->program_detail_max_date ? \Carbon\Carbon::parse($item->program_detail_max_date)->format('d/m/Y') : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-muted">Total Challan</td>
                            <td class="font-weight-bold text-primary">{{ ($item->generate_bill_count + $item->not_generate_bill_count) ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>

                <hr class="my-3">

                {{-- Statistics Info Boxes (matching Income module style) --}}
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-success mb-3" style="min-height: 70px;">
                            <span class="info-box-icon" style="height: 70px; line-height: 70px;"><i class="fas fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Bill Generated</span>
                                <span class="info-box-number">
                                    <a href="{{ route('admin.programDetail', [$item->id, 'bill_generated']) }}" class="text-white">{{ $item->generate_bill_count ?? 0 }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-danger mb-3" style="min-height: 70px;">
                            <span class="info-box-icon" style="height: 70px; line-height: 70px;"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Bill Not Generated</span>
                                <span class="info-box-number">
                                    <a href="{{ route('admin.programDetail', [$item->id, 'bill_not_generated']) }}" class="text-white">{{ $item->not_generate_bill_count ?? 0 }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-info mb-3" style="min-height: 70px;">
                            <span class="info-box-icon" style="height: 70px; line-height: 70px;"><i class="fas fa-exchange-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">After Posting</span>
                                <span class="info-box-number">
                                    <a href="{{ route('admin.programDetail', [$item->id, 'after_challan']) }}" class="text-white">{{ $item->after_challan_posting_count ?? 0 }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-secondary mb-3" style="min-height: 70px;">
                            <span class="info-box-icon" style="height: 70px; line-height: 70px;"><i class="fas fa-hourglass-half"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Before Posting</span>
                                <span class="info-box-number">
                                    <a href="{{ route('admin.programDetail', [$item->id, 'before_challan']) }}" class="text-white">{{ $item->before_challan_count ?? 0 }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-warning mb-3" style="min-height: 70px;">
                            <span class="info-box-icon" style="height: 70px; line-height: 70px;"><i class="fas fa-weight-hanging"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Not 12 MT</span>
                                <span class="info-box-number">
                                    <a href="{{ route('admin.programDetail', [$item->id, 'twelve_mt']) }}" class="text-white">{{ $item->not_twelve_mt ?? 0 }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box bg-teal mb-3" style="min-height: 70px;">
                            <span class="info-box-icon" style="height: 70px; line-height: 70px;"><i class="fas fa-gas-pump"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Petrol Pump</span>
                                <span class="info-box-number text-white">{{ $item->pump_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-3">

                {{-- Vendor Wise Posting --}}
                <form action="{{ route('challanPostingVendorReportshow') }}" method="POST" class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                    @csrf
                    <input type="hidden" name="mv_id" value="{{ $item->mother_vassel_id }}">
                    <span class="font-weight-bold" style="font-size:13px;"><i class="fas fa-truck mr-1 text-muted"></i>Vendor Wise Challan Posting</span>
                    <button type="submit" class="btn btn-dark btn-sm"><i class="fas fa-search mr-1"></i>Check Report</button>
                </form>

            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

{{-- ============================================= --}}
{{-- STYLES (matching Income module) --}}
{{-- ============================================= --}}
@section('style')
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        padding: 4px 8px;
        height: 31px !important;
        font-size: 13px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 28px;
    }
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
    // SELECT2 INITIALIZATION
    // =============================================
    $('.select2').select2({
        width: '100%',
        allowClear: true,
        minimumResultsForSearch: 10
    });

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
            { orderable: false, targets: [-1, -2, -3, -4, -5] } // Disable sorting on last 5 columns
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

    // =============================================
    // DELETE (kept from your original code)
    // =============================================
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    var deleteUrl = "{{ URL::to('/admin/program-delete') }}";

    $(document).on('click', '#deleteBtn', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this?')) return;

        var codeid = $(this).attr('rid');
        $.ajax({
            url: deleteUrl + '/' + codeid,
            method: "POST",
            data: { _method: 'DELETE' },
            success: function(d) {
                if (d.success) {
                    Swal.fire({
                        text: "Deleted Successfully",
                        icon: "success",
                        button: { text: "OK", className: "swal-button--confirm" }
                    }).then(() => { location.reload(); });
                }
            },
            error: function(d) {
                Swal.fire('Error!', 'Something went wrong.', 'error');
            }
        });
    });

});
</script>
@endsection