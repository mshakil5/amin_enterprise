@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-sitemap mr-2 text-primary"></i>Chart of Accounts Management
            </h3>
            <button type="button" class="btn btn-primary btn-sm" id="btn-show-form">
                <i class="fas fa-plus mr-1"></i> Add New Account
            </button>
        </div>

        {{-- Summary Info Boxes --}}
        <div class="row mb-3" id="summary-boxes">
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Accounts</span>
                        <span class="info-box-number" id="total-accounts">0</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Active Accounts</span>
                        <span class="info-box-number" id="active-accounts">0</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Inactive Accounts</span>
                        <span class="info-box-number" id="inactive-accounts">0</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-layer-group"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Heads</span>
                        <span class="info-box-number" id="total-heads">0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Account Form Card --}}
        <div class="card card-outline card-primary mb-4" id="chart-form-card" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-edit mr-1"></i>
                    <span id="form-title">Add New Account</span>
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" id="btn-close-form">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="form-alert-container"></div>
                <form class="form-horizontal" id="chart-form">
                    {{ csrf_field() }}
                    
                    {{-- Row 1 --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Account Head <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="account_head" id="account_head" required>
                                    <option value="">Select Head</option>
                                    <option value="Assets">Assets</option>
                                    <option value="Expenses">Expenses</option>
                                    <option value="Income">Income</option>
                                    <option value="Liabilities">Liabilities</option>
                                    <option value="Equity">Equity</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Account Sub Head <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="sub_account_head" id="sub_account_head" required>
                                    <option value="">Please Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Contingent <span class="text-danger">*</span></label>
                                <select class="form-control form-control-sm" name="contingent" id="contingent" required>
                                    <option value="Non-Contingent">Non-Contingent</option>
                                    <option value="Contingent">Contingent</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2 --}}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Account Name <span class="text-danger">*</span></label>
                                <input type="text" name="account_name" class="form-control form-control-sm" id="account_name" placeholder="Enter Account Name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Account Code <span class="text-danger">*</span></label>
                                <input type="number" name="serial" class="form-control form-control-sm" id="serial" placeholder="Ex: 1" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Description</label>
                                <textarea class="form-control form-control-sm" id="description" rows="1" name="description" placeholder="Enter description"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Row 3 (Petrol Pump - Hidden by default) --}}
                    <div class="row" id="petrol-pump-container" style="display: none;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold" style="font-size:13px;">Petrol Pump</label>
                                <select class="form-control form-control-sm select2" name="petrol_pumps_id" id="petrol_pumps_id">
                                    <option value="">Select Petrol Pump</option>
                                    @foreach ($petrolPumps as $pump)
                                        <option value="{{ $pump->id }}">{{ $pump->name }}</option>
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
                            <button type="submit" class="btn btn-primary btn-sm" id="btn-submit-form">
                                <i class="fas fa-save mr-1"></i>Save Account
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
                        <label class="sr-only">Account Head</label>
                        <select class="form-control form-control-sm" name="account_head" id="filter_account_head">
                            <option value="">All Account Heads</option>
                            @foreach ($accountHeads as $head)
                                <option value="{{ $head }}">{{ $head }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mx-sm-2">
                        <label class="sr-only">Sub Account Head</label>
                        <select class="form-control form-control-sm" name="sub_account_head" id="filter_sub_account_head">
                            <option value="">All Sub Heads</option>
                            @foreach ($subAccountHeads as $subHead)
                                <option value="{{ $subHead }}">{{ $subHead }}</option>
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

        {{-- Table Card --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i> Chart of Accounts Records
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="chartTBL" class="table table-bordered table-striped table-sm mb-0" style="font-size:12px;">
                        <thead>
                            <tr class="bg-dark text-white text-center">
                                <th style="width:40px">#</th>
                                <th>Account Name</th>
                                <th>Account Head</th>
                                <th>Sub Account Head</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th style="width:80px">Status</th>
                                <th style="width:100px">Action</th>
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
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single { padding: 4px 8px; height: 31px !important; font-size: 13px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 28px; }
    .info-box .info-box-number { font-size: 16px !important; }
    #chart-form-card { border-left: 4px solid #007bff; transition: all 0.3s ease; }
    #chart-form-card.edit-mode { border-left-color: #ffc107; }
    .form-control-sm:focus { border-color: #007bff; box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25); }
    .btn-action { padding: 2px 8px; font-size: 11px; margin-right: 3px; }
    #chart-form .form-group { margin-bottom: 10px; }
    #chart-form label { margin-bottom: 4px; }
    .switch { position: relative; display: inline-block; width: 34px; height: 18px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 18px;}
    .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 2px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%;}
    input:checked + .slider { background-color: #28a745; }
    input:checked + .slider:before { transform: translateX(16px); }
</style>
@endsection

@section('script')
<script>
 $(document).ready(function() {

    // =============================================
    // VARIABLES
    // =============================================
    var chartUrl = "{{ URL::to('/admin/chart-of-account') }}";
    var summaryUrl = "{{ route('admin.coa.summary') }}"; 
    var isEditMode = false;
    var editingId = null;

    // =============================================
    // SELECT2 INITIALIZATION
    // =============================================
    $('.select2').select2({ width: '100%', allowClear: true });

    // =============================================
    // LOAD SUMMARY
    // =============================================
    function loadSummary() {
        $.ajax({
            url: summaryUrl,
            type: 'GET',
            success: function(response) {
                $('#total-accounts').text(response.total_accounts);
                $('#active-accounts').text(response.active_accounts);
                $('#inactive-accounts').text(response.inactive_accounts);
                $('#total-heads').text(response.total_heads);
            }
        });
    }
    loadSummary();

    // =============================================
    // DATATABLE INITIALIZATION
    // =============================================
    var chartTBL = $('#chartTBL').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 100,
        ajax: {
            url: chartUrl,
            type: 'GET',
            data: function(d) {
                d.account_head = $('#filter_account_head').val();
                d.sub_account_head = $('#filter_sub_account_head').val();
            }
        },
        deferRender: true,
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'account_name', name: 'account_name', className: 'text-left' },
            { data: 'account_head', name: 'account_head', className: 'text-center' },
            { data: 'sub_account_head', name: 'sub_account_head', className: 'text-center' },
            { data: 'serial', name: 'serial', className: 'text-center' },
            { data: 'description', name: 'description', className: 'text-left' },
            { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[0, 'desc']],
        language: {
            search: "", searchPlaceholder: "Search...",
            emptyTable: "No chart of accounts found"
        },
        drawCallback: function() { loadSummary(); }
    });

    // =============================================
    // FILTER FORM
    // =============================================
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        chartTBL.ajax.reload();
    });

    $('#btn-reset-filter').on('click', function() {
        $('#filter_account_head').val('');
        $('#filter_sub_account_head').val('');
        chartTBL.ajax.reload();
    });

    // =============================================
    // FORM SHOW/HIDE
    // =============================================
    $('#btn-show-form').on('click', function() {
        resetForm();
        isEditMode = false;
        editingId = null;
        $('#form-title').text('Add New Account');
        $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Save Account');
        $('#chart-form-card').removeClass('edit-mode').slideDown(300);
        $('html, body').animate({ scrollTop: $('#chart-form-card').offset().top - 100 }, 300);
    });

    $('#btn-close-form, #btn-cancel-form').on('click', function() {
        $('#chart-form-card').slideUp(300);
        setTimeout(function() { resetForm(); }, 300);
    });

    // =============================================
    // ACCOUNT HEAD CHANGE (Sub Head & Petrol Pump logic)
    // =============================================
    $('#account_head').on('change', function() {
        var val = $(this).val();
        
        // Handle Petrol Pump
        if(val == "Liabilities") {
            $('#petrol-pump-container').show();
        } else {
            $('#petrol-pump-container').hide();
            $('#petrol_pumps_id').val('').trigger('change');
        }

        // Handle Sub Account Head
        var options = '<option value="">Please Select</option>';
        if(val == "Assets") {
            options += '<option value="Current Asset">Current Asset</option><option value="Fixed Asset">Fixed Asset</option><option value="Account Receivable">Account Receivable</option>';
        } else if(val == "Expenses") {
            options += '<option value="Cost Of Good Sold">Cost Of Good Sold</option><option value="Overhead Expense">Overhead Expense</option><option value="Operating Expense">Operating Expense</option><option value="Administrative Expense">Administrative Expense</option>';
        } else if(val == "Income") {
            options += '<option value="Direct Income">Direct Income</option><option value="Indirect Income">Indirect Income</option>';
        } else if(val == "Liabilities") {
            options += '<option value="Current Liabilities">Current Liabilities</option><option value="Long Term Liabilities">Long Term Liabilities</option><option value="Account Payable">Account Payable</option><option value="Short Term Liabilities">Short Term Liabilities</option>';
        } else if(val == "Equity") {
            options += '<option value="Equity Capital">Equity Capital</option><option value="Retained Earnings">Retained Earnings</option>';
        }
        $('#sub_account_head').html(options);
    });

    // =============================================
    // EDIT BUTTON CLICK
    // =============================================
    $('#chartTBL').on('click', '.edit-btn', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: chartUrl + '/' + id,
            type: 'GET',
            success: function(response) {
                isEditMode = true;
                editingId = id;

                $('#form-title').text('Edit Account - ' + response.account_name);
                $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Update Account');
                $('#chart-form-card').addClass('edit-mode').slideDown(300);

                $('#account_head').val(response.account_head).trigger('change');
                
                // Wait for sub account head dropdown to populate based on trigger above
                setTimeout(function() {
                    $('#sub_account_head').val(response.sub_account_head);
                }, 100);

                $('#contingent').val(response.contingent);
                $('#account_name').val(response.account_name);
                $('#serial').val(response.serial);
                $('#description').val(response.description);

                // Handle Petrol Pump Edit
                if(response.account_head == "Liabilities") {
                    $('#petrol-pump-container').show();
                    setTimeout(function() {
                        $('#petrol_pumps_id').val(response.petrol_pumps_id).trigger('change');
                    }, 100);
                } else {
                    $('#petrol-pump-container').hide();
                }

                $('html, body').animate({ scrollTop: $('#chart-form-card').offset().top - 100 }, 300);
            },
            error: function() {
                showToast('Error loading account data', 'error');
            }
        });
    });

    // =============================================
    // STATUS TOGGLE
    // =============================================
    $(document).on('click', '.status-btn', function() {
        var id = $(this).val();
        $.ajax({
            url: chartUrl + '/' + id + '/change-status',
            type: 'GET',
            success: function(response) {
                showToast('Status updated successfully', 'success');
                chartTBL.ajax.reload(null, false);
            },
            error: function() {
                showToast('Error updating status', 'error');
            }
        });
    });

    // =============================================
    // FORM SUBMISSION
    // =============================================
    $('#chart-form').on('submit', function(e) {
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

        var btn = $('#btn-submit-form');
        var originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                if (response.status === 200) {
                    showToast(response.message, 'success');
                    $('#chart-form-card').slideUp(300);
                    setTimeout(function() {
                        resetForm();
                        chartTBL.ajax.reload();
                    }, 300);
                } else if (response.status === 303) {
                    showFormAlert(response.message, 'warning');
                }
            },
            error: function(xhr) {
                var message = 'Error saving account';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
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
        $('#chart-form')[0].reset();
        $('#account_head').val('').trigger('change');
        $('#sub_account_head').html('<option value="">Please Select</option>');
        $('#petrol_pumps_id').val('').trigger('change');
        $('#petrol-pump-container').hide();
        
        isEditMode = false;
        editingId = null;
        $('#form-title').text('Add New Account');
        $('#btn-submit-form').html('<i class="fas fa-save mr-1"></i>Save Account');
        $('#chart-form-card').removeClass('edit-mode');
        $('#form-alert-container').html('');
    }

    // =============================================
    // ALERT FUNCTIONS
    // =============================================
    function showFormAlert(message, type) {
        type = type || 'warning';
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show py-2" role="alert">';
        alertHtml += '<i class="fas fa-exclamation-triangle mr-2"></i>' + message;
        alertHtml += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        alertHtml += '</div>';
        $('#form-alert-container').html(alertHtml);
    }

    function showToast(message, type) {
        type = type || 'info';
        var bg = { success: 'bg-success', error: 'bg-danger', warning: 'bg-warning', info: 'bg-info' };
        var toastHtml = '<div class="toast-container position-fixed top-0 right-0 p-3" style="z-index:9999;">';
        toastHtml += '<div class="toast show ' + bg[type] + ' text-white" role="alert" style="min-width:300px;">';
        toastHtml += '<div class="toast-header ' + bg[type] + ' text-white border-0"><strong class="mr-auto">Notification</strong>';
        toastHtml += '<button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button></div>';
        toastHtml += '<div class="toast-body" style="font-size:12px;">' + message + '</div></div></div>';
        $('body').append(toastHtml);
        setTimeout(function() { $('.toast-container').remove(); }, 4000);
    }

});
</script>
@endsection