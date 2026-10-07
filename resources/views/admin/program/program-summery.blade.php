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
                                <th>Trips</th>
                                <th>Trips as per bill</th>
                                <th>Bill Pending</th>
                                <th>Qty</th>
                                <th>Service Revenue</th>
                                <th>Carrying Bill</th>
                                <th>Gross Profit</th>
                                <th>Program Month</th>
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
                                <td class="text-center align-middle"><span class="badge badge-info">{{ $item->unique_challan_count ?? 0 }}</span></td>
                                <td class="text-center align-middle"><span class="badge badge-info">{{ $item->generate_bill_count ?? 0 }}</span></td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-info">{{ $item->not_generate_bill_count ?? 0 }}</span>
                                    @if(($item->not_generate_bill_count ?? 0) > 0)
                                    <button type="button" class="btn btn-warning btn-xs ml-1 btn-bill-receive"
                                            data-toggle="tooltip" title="Receive Bill"
                                            data-program-id="{{ $item->id ?? '' }}"
                                            data-client-id="{{ $item->client->id ?? '' }}"
                                            data-client-name="{{ $item->client->name ?? 'N/A' }}"
                                            data-mv-id="{{ $item->mother_vassel_id ?? '' }}"
                                            data-mv-name="{{ $item->motherVassel->name ?? 'N/A' }}"
                                            data-qty="{{ $item->total_dest_qty ?? 0 }}"
                                            data-carrying-bill="{{ $item->total_carrying_bill ?? 0 }}">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </button>
                                    @endif
                                </td>
                                
                                {{-- Sums Columns --}}
                                <td class="text-right align-middle">{{ number_format($item->total_dest_qty ?? 0, 2) }}</td>
                                <td class="text-right align-middle"> </td>
                                <td class="text-right align-middle">{{ number_format($item->total_carrying_bill ?? 0, 2) }}</td>
                                <td class="text-right align-middle"></td>
                                <td class="text-right align-middle"></td>
                            </tr>
                            @endforeach
                        </tbody>
                        {{-- ============================================= --}}
                        {{-- TABLE FOOTER (TOTALS) --}}
                        {{-- ============================================= --}}
                        <tfoot>
                            <tr class="bg-light font-weight-bold text-dark">
                                <td colspan="4" class="text-right align-middle">TOTAL</td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_dest_qty'), 2) }}</td>
                                <td class="text-right align-middle"></td>
                                <td class="text-right align-middle"></td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_carrying_bill'), 2) }}</td>
                                <td class="text-right align-middle"></td>
                                <td class="text-right align-middle">{{ number_format($data->sum('total_transportcost'), 2) }}</td>
                                <td class="text-right align-middle"></td>
                                <td class="text-right align-middle"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>



    </div>
</section>
{{-- ============================================= --}}
{{-- BILL RECEIVE MODAL --}}
{{-- ============================================= --}}
<div class="modal fade" id="billReceiveModal" tabindex="-1" role="dialog" aria-labelledby="billReceiveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="billReceiveForm" action="{{ route('admin.billReceives.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="billReceiveModalLabel"><i class="fas fa-money-bill-wave"></i> Receive Bill</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        {{-- Alert Placeholder --}}
                    <div id="billReceiveAlert" class="alert" style="display:none;"></div>
    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control form-control-sm" required value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bill Number <span class="text-danger">*</span></label>
                                <input type="text" name="bill_number" class="form-control form-control-sm" placeholder="Enter Bill Number" required>
                            </div>
                        </div>
                        
                        <!-- Hidden IDs -->
                        <input type="hidden" name="program_id" id="br_program_id">
                        <input type="hidden" name="client_id" id="br_client_id">
                        <input type="hidden" name="mother_vassel_id" id="br_mv_id">
                        <input type="hidden" name="status" value="1">
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="br_client_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mother Vessel</label>
                                <input type="text" class="form-control form-control-sm bg-light" id="br_mv_name" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Receive Type</label>
                                <select name="rcv_type" class="form-control form-control-sm">
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Adjustment">Adjustment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="text" name="qty" id="br_qty" class="form-control form-control-sm bg-light" readonly>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Total Amount</label>
                                <input type="number" step="0.01" name="total_amount" id="br_total_amount" class="form-control form-control-sm" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Maintenance</label>
                                <input type="number" step="0.01" name="maintainance" id="br_maintainance" class="form-control form-control-sm" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Scale Charge</label>
                                <input type="number" step="0.01" name="scale_charge" id="br_scale_charge" class="form-control form-control-sm" value="0.00">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Other Expense</label>
                                <input type="number" step="0.01" name="other_exp" id="br_other_exp" class="form-control form-control-sm" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Other Receive</label>
                                <input type="number" step="0.01" name="other_rcv" id="br_other_rcv" class="form-control form-control-sm" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Net Amount</label>
                                <input type="number" step="0.01" name="net_amount" id="br_net_amount" class="form-control form-control-sm bg-info font-weight-bold" readonly value="0.00">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Note</label>
                                <textarea name="note" class="form-control form-control-sm" rows="2" placeholder="Optional note..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save"></i> Save Bill Receive</button>
                </div>
            </form>
        </div>
    </div>
</div>

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





        // =============================================
    // BILL RECEIVE MODAL HANDLER
    // =============================================
    $('.btn-bill-receive').on('click', function() {
        let programId = $(this).data('program-id'); 
        let clientId = $(this).data('client-id');
        let clientName = $(this).data('client-name');
        let mvId = $(this).data('mv-id');
        let mvName = $(this).data('mv-name');
        let qty = $(this).data('qty');
        let carryingBill = $(this).data('carrying-bill');

        // Reset form and clear previous errors
        $('#billReceiveForm')[0].reset(); 
        $('#billReceiveAlert').hide().removeClass('alert-success alert-danger').html('');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Re-set the dynamically populated values because reset clears them
        $('#br_program_id').val(programId); 
        $('#br_client_id').val(clientId);
        $('#br_mv_id').val(mvId);
        $('#br_client_name').val(clientName);
        $('#br_mv_name').val(mvName);
        $('#br_qty').val(qty);
        $('#br_total_amount').val(carryingBill);
        $('input[name="date"]').val('{{ date("Y-m-d") }}');
        
        calculateNet();
        $('#billReceiveModal').modal('show');
    });

    // Calculate Net Amount dynamically
    function calculateNet() {
        let total = parseFloat($('#br_total_amount').val()) || 0;
        let maint = parseFloat($('#br_maintainance').val()) || 0;
        let scale = parseFloat($('#br_scale_charge').val()) || 0;
        let exp = parseFloat($('#br_other_exp').val()) || 0;
        let rcv = parseFloat($('#br_other_rcv').val()) || 0;
        
        let net = total - (maint + scale + exp) + rcv;
        $('#br_net_amount').val(net.toFixed(2));
    }

    $('#br_total_amount, #br_maintainance, #br_scale_charge, #br_other_exp, #br_other_rcv').on('input', function() {
        calculateNet();
    });

    // =============================================
    // AJAX FORM SUBMISSION
    // =============================================
    $('#billReceiveForm').on('submit', function(e) {
        e.preventDefault();
        
        let submitBtn = $(this).find('button[type="submit"]');
        let originalBtnHtml = submitBtn.html();
        
        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        // Clear previous errors
        $('#billReceiveAlert').hide().removeClass('alert-success alert-danger');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Show success message
                $('#billReceiveAlert').addClass('alert alert-success').html('<i class="fas fa-check-circle"></i> ' + response.message).show();
                
                // Close modal after 1.5 seconds and reload page to refresh data
                setTimeout(function() {
                    $('#billReceiveModal').modal('hide');
                    location.reload(); // Reload to update the "Bill Pending" counts
                }, 1500);
            },
            error: function(xhr) {
                // Reset button state
                submitBtn.prop('disabled', false).html(originalBtnHtml);

                if (xhr.status === 422) {
                    // Validation errors
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '<ul class="mb-0">';
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value[0] + '</li>';
                        // Highlight the invalid field
                        $('#' + key).addClass('is-invalid');
                    });
                    errorHtml += '</ul>';
                    $('#billReceiveAlert').addClass('alert alert-danger').html(errorHtml).show();
                } else {
                    // Server error (500, etc.)
                    let errorMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An unexpected error occurred. Please check the logs.';
                    $('#billReceiveAlert').addClass('alert alert-danger').html('<i class="fas fa-exclamation-triangle"></i> ' + errorMsg).show();
                }
            }
        });
    });


});
</script>
@endsection