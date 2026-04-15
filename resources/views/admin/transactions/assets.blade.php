@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-cubes mr-2 text-dark"></i>Asset Management
            </h3>
            <button type="button" class="btn btn-dark btn-sm" id="btn-show-form">
                <i class="fas fa-plus mr-1"></i> Add New Asset
            </button>
        </div>

        {{-- Summary Info Boxes --}}
        <div class="row mb-3">
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Purchase</span>
                        <span class="info-box-number" id="total-purchase">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Sold</span>
                        <span class="info-box-number" id="total-sold">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Depreciation</span>
                        <span class="info-box-number" id="total-depreciation">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Net Asset Value</span>
                        <span class="info-box-number" id="net-asset-value">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-gradient-success">
                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Inflow</span>
                        <span class="info-box-number" id="total-inflow">0.00</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="info-box bg-gradient-danger">
                    <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Outflow</span>
                        <span class="info-box-number" id="total-outflow">0.00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Asset Form Card --}}
        <div class="card card-outline card-dark mb-4" id="asset-form-card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-1"></i>
                    <span id="form-title">Add New Asset</span>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" id="btn-close-form">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="form-alert-container"></div>
                <form class="form-horizontal" id="asset-form">
                    {{ csrf_field() }}
                    <input type="hidden" name="asset_id" id="asset_id" value="">
                    <input type="hidden" name="tax_rate" id="tax_rate" value="">
                    <input type="hidden" name="tax_amount" id="tax_amount" value="">
                    <input type="hidden" name="vat_rate" value="">
                    <input type="hidden" name="vat_amount" value="">
                    <input type="hidden" name="at_amount" id="at_amount" value="">

                    {{-- Row 1: Date, Chart of Account, Reference, Transaction Type --}}
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
                                </label>
                                <select class="form-control select2" id="chart_of_account_id" name="chart_of_account_id" required>
                                    <option value="">Select chart of account</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}" data-type="{{ $account->sub_account_head }}">{{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Reference</label>
                                <input type="text" name="ref" class="form-control form-control-sm" id="ref" placeholder="Enter reference">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    Transaction Type <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-control-sm" id="transaction_type" name="transaction_type" required>
                                    <option value="">Select type</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Amount, Payment Type, Account --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    Amount <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="amount" class="form-control form-control-sm" id="amount" placeholder="0.00" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4" id="payment_type_container">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Account</label>
                                <select class="form-control select2" id="account_id" name="account_id">
                                    <option value="">Select account</option>
                                    @foreach($accountList as $account)
                                        <option value="{{ $account->id }}">{{ $account->type }} ({{ number_format($account->amount, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Description</label>
                                <input type="text" name="description" class="form-control form-control-sm" id="description" placeholder="Enter description">
                            </div>
                        </div>
                    </div>

                    {{-- Row 3: Conditional Payable/Receivable Holders --}}
                    <div class="row" id="holderRow" style="display: none;">
                        <div class="col-md-6" id="showpayable" style="display: none;">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    <i class="fas fa-file-invoice-dollar mr-1 text-warning"></i>Payable Holder
                                </label>
                                <select class="form-control select2" id="payable_holder_id" name="payable_holder_id">
                                    <option value="">Select payable holder</option>
                                    @foreach($payableAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" id="showreceivable" style="display: none;">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">
                                    <i class="fas fa-hand-holding-usd mr-1 text-success"></i>Receivable Holder
                                </label>
                                <select class="form-control select2" id="recivible_holder_id" name="recivible_holder_id">
                                    <option value="">Select receivable holder</option>
                                    @foreach($receivableAccounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->account_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-form">
                                <i class="fas fa-times mr-1"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-dark btn-sm" id="btn-submit-form">
                                <i class="fas fa-save mr-1"></i>Save Asset
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

        {{-- Asset Table Card --}}
        <div class="card card-dark card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i> Asset Records
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
                    <table id="assetTBL" class="table table-bordered table-striped table-sm mb-0" style="font-size:12px;">
                        <thead>
                            <tr class="bg-dark text-white text-center">
                                <th style="width:40px">#</th>
                                <th style="width:100px">Date</th>
                                <th style="width:180px">Account Head</th>
                                <th style="width:100px">Reference</th>
                                <th>Description</th>
                                <th style="width:120px">Type</th>
                                <th style="width:140px">Payment</th>
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
    
    /* =============================================
       OTHER STYLING
       ============================================= */
    .info-box .info-box-number {
        font-size: 16px !important;
    }
    
    #asset-form-card {
        border-left: 4px solid #343a40;
        transition: all 0.3s ease;
    }
    
    #asset-form-card.edit-mode {
        border-left-color: #6f42c1;
    }
    
    .form-control-sm:focus {
        border-color: #343a40;
        box-shadow: 0 0 0 0.2rem rgba(52, 58, 64, 0.25);
    }
    
    .btn-action {
        padding: 2px 8px;
        font-size: 11px;
        margin-right: 3px;
    }
    
    #asset-form .form-group {
        margin-bottom: 10px;
    }
    
    #asset-form label {
        margin-bottom: 4px;
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
    var chartUrl = "{{ URL::to('/admin/asset') }}";
    var summaryUrl = "{{ route('admin.asset.summary') }}";
    var isEditMode = false;
    var editingId = null;

    // =============================================
    // HELPER: Build Transaction Type Options
    // =============================================
    function buildTransactionOptions(accountType, selectedVal) {
        var dropdown = $('#transaction_type');
        dropdown.empty();
        dropdown.append('<option value="">Select type</option>');
        
        if (accountType === 'Fixed Asset') {
            dropdown.append('<option value="Purchase">Purchase</option>');
            dropdown.append('<option value="Sold">Sold</option>');
            dropdown.append('<option value="Depreciation">Depreciation</option>');
        } else {
            dropdown.append('<option value="Received">Received</option>');
            dropdown.append('<option value="Payment">Payment</option>');
        }
        
        if (selectedVal) {
            dropdown.val(selectedVal);
        }
    }

    // =============================================
    // HELPER: Build Payment Type Options
    // =============================================
    function buildPaymentOptions(transactionType, selectedVal) {
        var dropdown = $('#payment_type');
        dropdown.empty();
        dropdown.append('<option value="">Select type</option>');
        
        if (transactionType === 'Purchase') {
            dropdown.append('<option value="Account Payable">Account Payable</option>');
            dropdown.append('<option value="Cash">Cash</option>');
            dropdown.append('<option value="Bank">Bank</option>');
        } else if (transactionType === 'Sold') {
            dropdown.append('<option value="Account Receivable">Account Receivable</option>');
            dropdown.append('<option value="Cash">Cash</option>');
            dropdown.append('<option value="Bank">Bank</option>');
        } else {
            dropdown.append('<option value="Cash">Cash</option>');
            dropdown.append('<option value="Bank">Bank</option>');
        }
        
        if (selectedVal) {
            dropdown.val(selectedVal);
        }
    }

    // =============================================
    // HELPER: Toggle Holder Fields
    // =============================================
    function toggleHolderFields(paymentType) {
        $('#showpayable').hide();
        $('#showreceivable').hide();
        $('#holderRow').hide();
        
        if (paymentType === 'Account Payable') {
            $('#holderRow').show();
            $('#showpayable').show();
        } else if (paymentType === 'Account Receivable') {
            $('#holderRow').show();
            $('#showreceivable').show();
        }
        
        // Clear values when hiding
        if (paymentType !== 'Account Payable') {
            $('#payable_holder_id').val('').trigger('change');
        }
        if (paymentType !== 'Account Receivable') {
            $('#recivible_holder_id').val('').trigger('change');
        }
    }

    // =============================================
    // SELECT2 INITIALIZATION
    // =============================================
    function initSelect2() {
        $('.select2').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: $(this).find('option:first').text(),
                    minimumResultsForSearch: 10,
                    dropdownAutoWidth: false
                });
            }
        });
    }
    
    initSelect2();

    // =============================================
    // DYNAMIC DROPDOWN LOGIC
    // =============================================
    $('#chart_of_account_id').on('change', function() {
        var accountType = $(this).find(':selected').data('type');
        buildTransactionOptions(accountType, null);
        buildPaymentOptions(null, null);
        toggleHolderFields(null);
    });

    $('#transaction_type').on('change', function() {
        var transactionType = $(this).val();
        
        if (transactionType === 'Depreciation') {
            $('#payment_type_container').hide();
            $('#payment_type').val('');
            toggleHolderFields(null);
        } else {
            $('#payment_type_container').show();
            buildPaymentOptions(transactionType, null);
            toggleHolderFields(null);
        }
    });

    $('#payment_type').on('change', function() {
        toggleHolderFields($(this).val());
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
            data: { start_date: startDate, end_date: endDate },
            success: function(response) {
                $('#total-purchase').text(response.total_purchase);
                $('#total-sold').text(response.total_sold);
                $('#total-depreciation').text(response.total_depreciation);
                $('#net-asset-value').text(response.net_asset_value);
                $('#total-inflow').text(response.total_inflow);
                $('#total-outflow').text(response.total_outflow);
            }
        });
    }
    loadSummary();

    // =============================================
    // DATATABLE
    // =============================================
    var assetTBL = $('#assetTBL').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: chartUrl,
            type: 'GET',
            data: function(d) {
                d.start_date = $('#filter_start_date').val();
                d.end_date = $('#filter_end_date').val();
                d.account_name = $('#filter_account_name').val();
            }
        },
        deferRender: true,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy', className: 'btn btn-sm btn-secondary', text: '<i class="fas fa-copy"></i> Copy' },
            { extend: 'csv', className: 'btn btn-sm btn-success', text: '<i class="fas fa-file-csv"></i> CSV' },
            { extend: 'excel', className: 'btn btn-sm btn-primary', text: '<i class="fas fa-file-excel"></i> Excel' },
            { extend: 'pdf', className: 'btn btn-sm btn-danger', text: '<i class="fas fa-file-pdf"></i> PDF' },
            { extend: 'print', className: 'btn btn-sm btn-dark', text: '<i class="fas fa-print"></i> Print' }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'date', name: 'date', render: function(data) { return data ? dayjs(data).format('DD-MM-YYYY') : ''; } },
            { data: 'chart_of_account', name: 'chart_of_account', className: 'text-left' },
            { data: 'ref', name: 'ref', className: 'text-center' },
            { data: 'description', name: 'description', className: 'text-left' },
            { data: 'tran_type_badge', name: 'tran_type_badge', orderable: false, searchable: false, className: 'text-center' },
            { data: 'payment_badge', name: 'payment_badge', orderable: false, searchable: false, className: 'text-center' },
            { data: 'amount_formatted', name: 'amount', className: 'text-right' },
            {
                data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center',
                render: function(data, type, row) {
                    let btn = '<button type="button" class="btn btn-warning btn-action edit-btn" data-id="' + row.id + '" title="Edit"><i class="fas fa-edit"></i> Edit</button>';
                    let voucherUrl = "{{ route('admin.expense.voucher', ['id' => '__id__']) }}".replace('__id__', row.id);
                    btn += '<a href="' + voucherUrl + '" target="_blank" class="btn btn-info btn-action" title="Voucher"><i class="fas fa-receipt"></i></a>';
                    let reverseUrl = "{{ route('admin.transactions.reverse', ['id' => '__id__']) }}".replace('__id__', row.id);
                    btn += '<a href="' + reverseUrl + '" class="btn btn-success btn-action" title="Reverse" onclick="return confirm(\'Are you sure to reverse?\')"><i class="fas fa-undo"></i></a>';
                    return btn;
                }
            }
        ],
        order: [[1, 'desc']],
        language: { search: "", searchPlaceholder: "Search...", emptyTable: "No asset records found", zeroRecords: "No matching records found" },
        drawCallback: function() { loadSummary(); }
    });

    $('.dt-buttons').hide();
    $('#btn-copy').on('click', function() { assetTBL.button(0).trigger(); });
    $('#btn-csv').on('click', function() { assetTBL.button(1).trigger(); });
    $('#btn-excel').on('click', function() { assetTBL.button(2).trigger(); });
    $('#btn-pdf').on('click', function() { assetTBL.button(3).trigger(); });
    $('#btn-print').on('click', function() { assetTBL.button(4).trigger(); });

    // =============================================
    // FILTER FORM
    // =============================================
    $('#filter-form').on('submit', function(e) { e.preventDefault(); assetTBL.ajax.reload(); });
    $('#btn-reset-filter').on('click', function() {
        $('#filter_start_date').val('');
        $('#filter_end_date').val('');
        $('#filter_account_name').val('').trigger('change');
        assetTBL.ajax.reload();
    });

    // =============================================
    // FORM SHOW/HIDE
    // =============================================
    $('#btn-show-form').on('click', function() {
        resetForm();
        isEditMode = false;
        editingId = null;
        $('#form-title').text('Add New Asset');
        $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Save Asset');
        $('#asset-form-card').removeClass('edit-mode').slideDown(300);
        $('html, body').animate({ scrollTop: $('#asset-form-card').offset().top - 100 }, 300);
    });

    $('#btn-close-form, #btn-cancel-form').on('click', function() {
        $('#asset-form-card').slideUp(300);
        setTimeout(resetForm, 300);
    });

    // =============================================
    // EDIT BUTTON
    // =============================================
    $('#assetTBL').on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: chartUrl + '/' + id,
            type: 'GET',
            beforeSend: function(request) {
                return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
            },
            success: function(response) {
                isEditMode = true;
                editingId = id;
                $('#form-title').text('Edit Asset - ' + (response.tran_id || 'ID: ' + response.id));
                $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Update Asset');
                $('#asset-form-card').addClass('edit-mode').slideDown(300);

                $('#asset_id').val(response.id);
                $('#date').val(response.date);
                $('#ref').val(response.ref || '');
                $('#amount').val(response.amount);
                $('#description').val(response.description || '');
                $('#chart_of_account_id').val(response.chart_of_account_id).trigger('change');
                $('#account_id').val(response.account_id || '').trigger('change');

                // Rebuild dropdowns based on saved type
                var accType = response.chart_of_account_type;
                var transType = response.transaction_type;
                var payType = response.payment_type;

                setTimeout(function() {
                    buildTransactionOptions(accType, transType);
                    
                    if (transType === 'Depreciation') {
                        $('#payment_type_container').hide();
                        $('#holderRow').hide();
                    } else {
                        $('#payment_type_container').show();
                        buildPaymentOptions(transType, payType);
                        toggleHolderFields(payType);
                    }

                    $('#payable_holder_id').val(response.payable_holder_id || '').trigger('change');
                    $('#recivible_holder_id').val(response.recivible_holder_id || '').trigger('change');
                }, 300);

                $('html, body').animate({ scrollTop: $('#asset-form-card').offset().top - 100 }, 300);
            },
            error: function() { showToast('Error loading asset data', 'error'); }
        });
    });

    // =============================================
    // FORM SUBMISSION
    // =============================================
    $('#asset-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var url = (isEditMode && editingId) ? chartUrl + '/' + editingId : chartUrl;
        var method = (isEditMode && editingId) ? 'PUT' : 'POST';

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
                    $('#asset-form-card').slideUp(300);
                    setTimeout(function() { resetForm(); assetTBL.ajax.reload(); }, 300);
                } else if (response.status === 303) {
                    showFormAlert(response.message, 'warning');
                }
            },
            error: function(xhr) {
                if (xhr.status === 419) { showToast('Session expired. Please refresh.', 'error'); return; }
                var message = 'Error saving asset';
                if (xhr.responseJSON && xhr.responseJSON.message) message = xhr.responseJSON.message;
                else if (xhr.responseJSON && xhr.responseJSON.errors) message = Object.values(xhr.responseJSON.errors).flat().join(', ');
                showFormAlert(message, 'danger');
            },
            complete: function() { btn.html(originalText).prop('disabled', false); }
        });
    });

    // =============================================
    // RESET FORM
    // =============================================
    function resetForm() {
        $('#asset-form')[0].reset();
        $('#asset_id').val('');
        $('#date').val('{{ date("Y-m-d") }}');
        isEditMode = false;
        editingId = null;

        $('#chart_of_account_id').val('').trigger('change');
        $('#account_id').val('').trigger('change');
        $('#payable_holder_id').val('').trigger('change');
        $('#recivible_holder_id').val('').trigger('change');

        // Reset dynamic dropdowns
        var dropdown = $('#transaction_type');
        dropdown.empty();
        dropdown.append('<option value="">Select type</option>');

        var payDropdown = $('#payment_type');
        payDropdown.empty();
        payDropdown.append('<option value="">Select type</option>');
        payDropdown.append('<option value="Cash">Cash</option>');
        payDropdown.append('<option value="Bank">Bank</option>');

        $('#payment_type_container').show();
        $('#holderRow, #showpayable, #showreceivable').hide();

        $('#form-title').text('Add New Asset');
        $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Save Asset');
        $('#asset-form-card').removeClass('edit-mode');
        $('#form-alert-container').html('');
    }

    // =============================================
    // ALERT FUNCTIONS
    // =============================================
    function showFormAlert(message, type) {
        var icons = { success:'fas fa-check-circle', warning:'fas fa-exclamation-triangle', danger:'fas fa-exclamation-circle' };
        var html = '<div class="alert alert-' + type + ' alert-dismissible fade show py-2" role="alert">';
        html += '<i class="' + icons[type] + ' mr-2"></i>' + message;
        html += '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
        $('#form-alert-container').html(html);
        setTimeout(function() { $('#form-alert-container .alert').alert('close'); }, 5000);
    }

    function showToast(message, type) {
        var icons = { success:'fas fa-check-circle', error:'fas fa-exclamation-circle', warning:'fas fa-exclamation-triangle', info:'fas fa-info-circle' };
        var bg = { success:'bg-success', error:'bg-danger', warning:'bg-warning', info:'bg-info' };
        var html = '<div class="toast-container position-fixed top-0 right-0 p-3" style="z-index:9999;">';
        html += '<div class="toast show ' + bg[type] + ' text-white" role="alert" style="min-width:300px;">';
        html += '<div class="toast-header ' + bg[type] + ' text-white border-0" style="font-size:12px;">';
        html += '<i class="' + icons[type] + ' mr-2"></i><strong class="mr-auto">' + type.charAt(0).toUpperCase() + type.slice(1) + '</strong>';
        html += '<button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button></div>';
        html += '<div class="toast-body" style="font-size:12px;">' + message + '</div></div></div>';
        $('body').append(html);
        setTimeout(function() { $('.toast-container').remove(); }, 4000);
    }

});
</script>
@endsection