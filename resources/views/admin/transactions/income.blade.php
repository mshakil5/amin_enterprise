@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-hand-holding-usd mr-2 text-success"></i>Income Management
            </h3>
            <button type="button" class="btn btn-success btn-sm" id="btn-show-form">
                <i class="fas fa-plus mr-1"></i> Add New Income
            </button>
        </div>

        {{-- Summary Info Boxes --}}
        <div class="row mb-3" id="summary-boxes">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Income</span>
                        <span class="info-box-number" id="total-income">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Refund</span>
                        <span class="info-box-number" id="total-refund">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Net Income</span>
                        <span class="info-box-number" id="net-income">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-calendar-day"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Today's Income</span>
                        <span class="info-box-number" id="today-income">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">This Month</span>
                        <span class="info-box-number" id="month-income">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-list-ol"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Records</span>
                        <span class="info-box-number" id="total-count">0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Income Form Card (Collapsible) --}}
        <div class="card card-outline card-success mb-4" id="income-form-card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-1"></i>
                    <span id="form-title">Add New Income</span>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" id="btn-close-form">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="form-alert-container"></div>
                <form class="form-horizontal" id="income-form">
                    {{ csrf_field() }}
                    <input type="hidden" name="income_id" id="income_id" value="">

                    {{-- Row 1: Date, Chart of Account, Reference --}}
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="date" class="form-control form-control-sm" id="date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    Chart of Account <span class="text-danger">*</span>
                                </label> <br>
                                <select class="form-control form-control-sm select2" id="chart_of_account_id" name="chart_of_account_id" required>
                                    <option value="">Select chart of account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Reference</label>
                                <input type="text" name="ref" class="form-control form-control-sm" id="ref" placeholder="Enter reference">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    Transaction Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-control-sm" id="transaction_type" name="transaction_type" required>
                                    <option value="Current">New Income</option>
                                    <option value="Refund">Income Refund</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Amount, Tax, Payment --}}
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="amount" class="form-control form-control-sm" id="amount" placeholder="0.00" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Tax %</label>
                                <input type="number" name="tax_rate" class="form-control form-control-sm" id="tax_rate" placeholder="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Tax Amount</label>
                                <input type="number" name="tax_amount" class="form-control form-control-sm" id="tax_amount" placeholder="0.00" step="0.01" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Total Amount</label>
                                <input type="number" name="at_amount" class="form-control form-control-sm" id="at_amount" placeholder="0.00" step="0.01" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    Payment Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-control-sm" id="payment_type" name="payment_type">
                                    <option value="">Select type</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Account</label>
                                <select class="form-control form-control-sm" id="account_id" name="account_id">
                                    <option value="">Select account</option>
                                    @foreach($accountList as $account)
                                        <option value="{{ $account->id }}">{{ $account->type }} ({{ number_format($account->amount, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Mother Vessel, Vendor, Sequence --}}
                    <div class="row" id="vendorDiv" style="display: none;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Mother Vessel</label>
                                <select class="form-control form-control-sm select2" id="mother_vassel_id" name="mother_vassel_id">
                                    <option value="">Select Mother Vessel</option>
                                    @foreach (\App\Models\MotherVassel::where('status', 1)->select('id', 'name')->get() as $mv)
                                        <option value="{{ $mv->id }}">{{ $mv->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Vendor</label>
                                <select class="form-control form-control-sm select2" id="vendor_id" name="vendor_id">
                                    <option value="">Select Vendor</option>
                                    @foreach (\App\Models\Vendor::where('status', 1)->orderby('id', 'DESC')->select('id', 'name')->get() as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Vendor Sequence No.</label>
                                <select class="form-control form-control-sm select2" id="vendor_sequence_id" name="vendor_sequence_id">
                                    <option value="">Select Sequence Number</option>
                                    @foreach (\App\Models\VendorSequenceNumber::where('status', 1)->select('id', 'unique_id')->get() as $vsno)
                                        <option value="{{ $vsno->id }}">{{ $vsno->unique_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Description (when vendor div is hidden) --}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Description</label>
                                <textarea class="form-control form-control-sm" id="description" rows="2" name="description" placeholder="Enter description"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-form">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-success btn-sm" id="btn-submit-form">
                                <i class="fas fa-save mr-1"></i>Save Income
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Filter Card --}}
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
                <form class="form-inline" id="filter-form" role="form">
                    <div class="form-group mx-sm-2">
                        <label class="sr-only">Start Date</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            </div>
                            <input type="date" class="form-control" name="start_date" id="filter_start_date">
                        </div>
                    </div>
                    <div class="form-group mx-sm-2">
                        <label class="sr-only">End Date</label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                            </div>
                            <input type="date" class="form-control" name="end_date" id="filter_end_date">
                        </div>
                    </div>
                    <div class="form-group mx-sm-2">
                        <label class="sr-only">Account</label>
                        <select class="form-control form-control-sm select2" name="account_name" id="filter_account_name" style="width: 200px;">
                            <option value="">All Accounts</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->account_name }}">{{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search mr-1"></i>Search
                    </button>
                    <button type="button" class="btn btn-default btn-sm" id="btn-reset-filter">
                        <i class="fas fa-redo mr-1"></i>Reset
                    </button>
                </form>
            </div>
        </div>

        {{-- Income Table Card --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i> Income Records
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
                    <table id="incomeTBL" class="table table-bordered table-striped table-sm mb-0" style="font-size:12px;">
                        <thead>
                            <tr class="bg-dark text-white text-center">
                                <th style="width:40px">#</th>
                                <th style="width:100px">Date</th>
                                <th style="width:150px">Chart of Account</th>
                                <th style="width:100px">Reference</th>
                                <th>Description</th>
                                <th style="width:100px">Type</th>
                                <th style="width:100px">Payment</th>
                                <th style="width:120px">Amount</th>
                                <th style="width:150px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

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
    #income-form-card {
        border-left: 4px solid #28a745;
        transition: all 0.3s ease;
    }
    #income-form-card.edit-mode {
        border-left-color: #ffc107;
    }
    .form-control-sm:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    .btn-action {
        padding: 2px 8px;
        font-size: 11px;
        margin-right: 3px;
    }
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
 $(document).ready(function() {

    // =============================================
    // VARIABLES
    // =============================================
    var chartUrl = "{{ URL::to('/admin/income') }}";
    var summaryUrl = "{{ route('admin.income.summary') }}";
    var isEditMode = false;
    var editingId = null;

    // =============================================
    // SELECT2 INITIALIZATION
    // =============================================
    $('.select2').select2({
        width: '100%',
        theme: 'bootstrap4',
        allowClear: true
    });

    // =============================================
    // LOAD SUMMARY
    // =============================================
    function loadSummary() {
        var startDate = $('#filter_start_date').val();
        var endDate = $('#filter_end_date').val();

        $.ajax({
            url: summaryUrl,
            type: 'GET',
            data: {
                start_date: startDate,
                end_date: endDate
            },
            success: function(response) {
                $('#total-income').text(response.total_income);
                $('#total-refund').text(response.total_refund);
                $('#net-income').text(response.net_income);
                $('#today-income').text(response.today_income);
                $('#month-income').text(response.month_income);
                $('#total-count').text(response.total_count);
            }
        });
    }
    loadSummary();

    // =============================================
    // DATATABLE INITIALIZATION
    // =============================================
    var incomeTBL = $('#incomeTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: chartUrl,
            type: 'GET',
            data: function(d) {
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
                d.account_name = $('#filter_account_name').val();
            },
            error: function(xhr, error, thrown) {
                console.log(xhr.responseText);
            }
        },
        deferRender: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-secondary',
                text: '<i class="fas fa-copy"></i> Copy'
            },
            {
                extend: 'csv',
                className: 'btn btn-sm btn-success',
                text: '<i class="fas fa-file-csv"></i> CSV'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-primary',
                text: '<i class="fas fa-file-excel"></i> Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger',
                text: '<i class="fas fa-file-pdf"></i> PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-dark',
                text: '<i class="fas fa-print"></i> Print'
            }
        ],
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'date',
                name: 'date',
                render: function(data, type, row) {
                    return data ? dayjs(data).format('DD-MM-YYYY') : '';
                }
            },
            {
                data: 'chart_of_account',
                name: 'chart_of_account',
                className: 'text-left'
            },
            { data: 'ref', name: 'ref', className: 'text-center' },
            { data: 'description', name: 'description', className: 'text-left' },
            {
                data: 'tran_type_badge',
                name: 'tran_type_badge',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'payment_badge',
                name: 'payment_badge',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'amount_formatted',
                name: 'amount',
                className: 'text-right'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row, meta) {
                    let buttons = '';

                    buttons += '<button type="button" class="btn btn-warning btn-action edit-btn" data-id="' + row.id + '" title="Edit">';
                    buttons += '<i class="fas fa-edit"></i> Edit</button>';

                    let voucherUrl = "{{ route('admin.expense.voucher', ['id' => '__id__']) }}".replace('__id__', row.id);
                    buttons += '<a href="' + voucherUrl + '" target="_blank" class="btn btn-info btn-action" title="Voucher">';
                    buttons += '<i class="fas fa-receipt"></i></a>';

                    let reverseUrl = "{{ route('admin.transactions.reverse', ['id' => '__id__']) }}".replace('__id__', row.id);
                    buttons += '<a href="' + reverseUrl + '" class="btn btn-success btn-action" title="Reverse" onclick="return confirm(\'Are you sure to reverse this transaction?\')">';
                    buttons += '<i class="fas fa-undo"></i></a>';

                    return buttons;
                }
            }
        ],
        order: [[1, 'desc']],
        language: {
            search: "",
            searchPlaceholder: "Search...",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "No entries available",
            emptyTable: "No income records found",
            zeroRecords: "No matching records found"
        },
        drawCallback: function() {
            loadSummary();
        }
    });

    // Hide export buttons from top (we have custom ones)
    $('.dt-buttons').hide();

    // Bind custom export buttons
    $('#btn-copy').on('click', function() { incomeTBL.button(0).trigger(); });
    $('#btn-csv').on('click', function() { incomeTBL.button(1).trigger(); });
    $('#btn-excel').on('click', function() { incomeTBL.button(2).trigger(); });
    $('#btn-pdf').on('click', function() { incomeTBL.button(3).trigger(); });
    $('#btn-print').on('click', function() { incomeTBL.button(4).trigger(); });

    // =============================================
    // FILTER FORM
    // =============================================
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        incomeTBL.ajax.reload();
    });

    $('#btn-reset-filter').on('click', function() {
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        $('#filter_account_name').val('').trigger('change');
        incomeTBL.ajax.reload();
    });

    // =============================================
    // FORM SHOW/HIDE
    // =============================================
    $('#btn-show-form').on('click', function() {
        resetForm();
        isEditMode = false;
        editingId = null;
        $('#form-title').text('Add New Income');
        $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Save Income');
        $('#income-form-card').removeClass('edit-mode').slideDown(300);
        $('html, body').animate({
            scrollTop: $('#income-form-card').offset().top - 100
        }, 300);
    });

    $('#btn-close-form, #btn-cancel-form').on('click', function() {
        $('#income-form-card').slideUp(300);
        setTimeout(function() {
            resetForm();
        }, 300);
    });

    // =============================================
    // VENDOR DIV TOGGLE
    // =============================================
    $('#chart_of_account_id').on('change', function() {
        let selectedText = $(this).find('option:selected').text().toLowerCase();
        if (selectedText.includes("token fee") || selectedText.includes("token") || selectedText.includes("discount")) {
            $('#vendorDiv').show();
            $('#descriptionRow').hide();
            // Move description value
            $('#vendorDiv #description').val($('#descriptionMain').val());
        } else {
            $('#vendorDiv').hide();
            $('#descriptionRow').show();
            // Move description value
            $('#descriptionMain').val($('#vendorDiv #description').val());
        }
    });

    // =============================================
    // VENDOR SEQUENCE AJAX
    // =============================================
    $('#vendor_id').on('change', function() {
        var vendorId = $(this).val();
        var $sequenceSelect = $('#vendor_sequence_id');
        $sequenceSelect.empty().append('<option value="">Select Sequence Number</option>');

        if (vendorId) {
            $.ajax({
                url: '/admin/vendor/' + vendorId + '/sequences',
                type: 'GET',
                success: function(data) {
                    $.each(data, function(key, sequence) {
                        $sequenceSelect.append('<option value="' + sequence.id + '">' + sequence.unique_id + '</option>');
                    });
                }
            });
        }
    });

    // =============================================
    // TAX CALCULATION
    // =============================================
    function calculateTotal() {
        var amount = parseFloat($('#amount').val()) || 0;
        var taxRate = parseFloat($('#tax_rate').val()) || 0;
        var taxAmount = amount * (taxRate / 100);
        var totalAmount = amount + taxAmount;

        $('#tax_amount').val(taxAmount.toFixed(2));
        $('#at_amount').val(totalAmount.toFixed(2));
    }

    $(document).on('input', '#amount, #tax_rate', calculateTotal);

    // =============================================
    // EDIT BUTTON CLICK
    // =============================================
    $('#incomeTBL').on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: chartUrl + '/' + id,
            type: 'GET',
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function(response) {

                console.log(response);

                isEditMode = true;
                editingId = id;

                // Update form title and button
                $('#form-title').text('Edit Income - ' + (response.tran_id || 'ID: ' + response.id));
                $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Update Income');
                $('#income-form-card').addClass('edit-mode').slideDown(300);

                // Populate form fields
                $('#income_id').val(response.id);
                $('#date').val(response.date);
                $('#ref').val(response.ref || '');
                $('#transaction_type').val(response.transaction_type);
                $('#amount').val(response.amount);
                $('#tax_rate').val(response.tax_rate || '');
                $('#tax_amount').val(response.tax_amount || '');
                $('#at_amount').val(response.at_amount || '');
                $('#payment_type').val(response.payment_type || '');
                $('#chart_of_account_id').val(response.chart_of_account_id).trigger('change');
                $('#account_id').val(response.account_id || '').trigger('change');
                $('#mother_vassel_id').val(response.mother_vassel_id || '').trigger('change');

                // Handle description based on vendor div visibility
                if (response.vendor_id) {
                    $('#vendorDiv').show();
                    $('#descriptionRow').hide();
                    $('#vendor_id').val(response.vendor_id).trigger('change');

                    // Load vendor sequences after vendor is set
                    setTimeout(function() {
                        $('#vendor_sequence_id').val(response.vendor_sequence_number_id || '').trigger('change');
                    }, 500);

                    $('#vendorDiv #description').val(response.description || '');
                } else {
                    $('#vendorDiv').hide();
                    $('#descriptionRow').show();
                    $('#description').val(response.description || '');
                }

                // Recalculate totals
                calculateTotal();

                // Scroll to form
                $('html, body').animate({
                    scrollTop: $('#income-form-card').offset().top - 100
                }, 300);
            },
            error: function(xhr) {
                showToast('Error loading income data', 'error');
            }
        });
    });

    // =============================================
    // FORM SUBMISSION
    // =============================================
    $('#income-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        var url, method;

        if (isEditMode && editingId) {
            url = chartUrl + '/' + editingId;
            method = 'PUT';
        } else {
            url = chartUrl;
            method = 'POST';
        }

        // Show loading state
        var btn = $('#btn-submit-form');
        var originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function(response) {
                if (response.status === 200) {
                    showToast(response.message, 'success');
                    $('#income-form-card').slideUp(300);
                    setTimeout(function() {
                        resetForm();
                        incomeTBL.ajax.reload();
                    }, 300);
                } else if (response.status === 303) {
                    showFormAlert(response.message, 'warning');
                }
            },
            error: function(xhr) {
                if (xhr.status === 419) {
                    showToast('Session expired. Please refresh the page.', 'error');
                    return;
                }
                var message = 'Error saving income';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = Object.values(xhr.responseJSON.errors).flat();
                    message = errors.join(', ');
                }
                showFormAlert(message, 'danger');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // =============================================
    // RESET FORM
    // =============================================
    function resetForm() {
        $('#income-form')[0].reset();
        $('#income_id').val('');
        $('#date').val('{{ date("Y-m-d") }}');
        isEditMode = false;
        editingId = null;

        // Reset select2
        $('#chart_of_account_id').val('').trigger('change');
        $('#account_id').val('').trigger('change');
        $('#mother_vassel_id').val('').trigger('change');
        $('#vendor_id').val('').trigger('change');
        $('#vendor_sequence_id').val('').trigger('change').empty().append('<option value="">Select Sequence Number</option>');

        // Reset calculated fields
        $('#tax_amount').val('');
        $('#at_amount').val('');

        // Hide vendor div
        $('#vendorDiv').hide();
        $('#descriptionRow').show();

        // Reset form state
        $('#form-title').text('Add New Income');
        $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Save Income');
        $('#income-form-card').removeClass('edit-mode');

        // Clear alerts
        $('#form-alert-container').html('');
    }

    // =============================================
    // ALERT FUNCTIONS
    // =============================================
    function showFormAlert(message, type) {
        type = type || 'warning';
        var icons = {
            success: 'fas fa-check-circle',
            warning: 'fas fa-exclamation-triangle',
            danger: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show py-2" role="alert">';
        alertHtml += '<i class="' + icons[type] + ' mr-2"></i>' + message;
        alertHtml += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        alertHtml += '</div>';
        $('#form-alert-container').html(alertHtml);

        // Auto hide after 5 seconds
        setTimeout(function() {
            $('#form-alert-container .alert').alert('close');
        }, 5000);
    }

    function showToast(message, type) {
        type = type || 'info';
        var icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        var bg = {
            success: 'bg-success',
            error: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info'
        };

        var toastHtml = '<div class="toast-container position-fixed top-0 right-0 p-3" style="z-index:9999;">';
        toastHtml += '<div class="toast show ' + bg[type] + ' text-white" role="alert" style="min-width:300px;">';
        toastHtml += '<div class="toast-header ' + bg[type] + ' text-white border-0" style="font-size:12px;">';
        toastHtml += '<i class="' + icons[type] + ' mr-2"></i>';
        toastHtml += '<strong class="mr-auto">' + type.charAt(0).toUpperCase() + type.slice(1) + '</strong>';
        toastHtml += '<button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button>';
        toastHtml += '</div>';
        toastHtml += '<div class="toast-body" style="font-size:12px;">' + message + '</div>';
        toastHtml += '</div></div>';

        $('body').append(toastHtml);
        setTimeout(function() {
            $('.toast-container').remove();
        }, 4000);
    }

});
</script>
@endsection