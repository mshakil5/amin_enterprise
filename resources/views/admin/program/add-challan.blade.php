@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-plus-circle mr-2 text-success"></i>Add New Challan
            </h3>
            <a href="{{ route('admin.programDetail', $data->id) }}" class="btn btn-outline-dark btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Details
            </a>
        </div>

        {{-- Program Summary Info --}}
        <div class="row mb-3">
            <div class="col-lg-2 col-md-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-building"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Client</span>
                        <span class="info-box-number text-sm">{{ $data->client->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-ship"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Mother Vessel</span>
                        <span class="info-box-number text-sm">{{ $data->motherVassel->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-map-marker-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ghat</span>
                        <span class="info-box-number text-sm">{{ $data->ghat->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Consignment</span>
                        <span class="info-box-number text-sm">{{ $data->consignmentno ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-truck mr-1"></i> Challan Entries</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="form-alert-container"></div>
                
                {{-- Added autocomplete="off" to fix Chrome's duplicate node/autofill warnings --}}
                <form id="createThisForm" action="{{ route('admin.program.storeChallan', $data->id) }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="hidden" name="program_id" id="program_id" value="{{ $data->id }}">
                    
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="font-weight-bold" style="font-size:13px;">Program Date</label>
                            <input type="date" class="form-control form-control-sm" value="{{ $data->date ?? '' }}" readonly>
                        </div>
                        <div class="col-md-3">
                            <label class="font-weight-bold" style="font-size:13px;">Challan Date <span class="text-danger">*</span></label>
                            <input type="date" name="newDate" id="newDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="programTable">
                            <thead class="bg-dark text-white text-center" style="font-size: 12px;">
                                <tr>
                                    <th style="width: 150px;">Vendor</th>
                                    <th style="width: 120px;">Truck#</th>
                                    <th style="width: 100px;">Challan</th>
                                    <th style="width: 100px;">Cash Adv</th>
                                    <th style="width: 100px;">Fuel Qty</th>
                                    <th style="width: 100px;">Fuel Rate</th>
                                    <th style="width: 110px;">Fuel Adv</th>
                                    <th style="width: 100px;">Fuel Token</th>
                                    <th style="width: 140px;">Pump</th>
                                    <th style="width: 110px;">Total</th>
                                    <th style="width: 50px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><select class="form-control form-control-sm select2 vendor-select" name="vendor_id[]"><option value="">Select</option>@foreach ($vendors as $vendor)<option value="{{ $vendor->id }}">{{ $vendor->name }}</option>@endforeach</select></td>
                                    <td><input type="text" class="form-control form-control-sm" name="truck_number[]"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="challan_no[]"></td>
                                    <td><input type="number" class="form-control form-control-sm cashamount" name="cashamount[]"></td>
                                    <td><input type="number" class="form-control form-control-sm fuelqty" name="fuelqty[]"></td>
                                    <td><input type="number" class="form-control form-control-sm fuel_rate" name="fuel_rate[]" value="115"></td>
                                    <td><input type="number" class="form-control form-control-sm fuel_amount" name="fuel_amount[]" readonly></td>
                                    <td><input type="number" class="form-control form-control-sm" name="fueltoken[]"></td>
                                    <td><select name="petrol_pump_id[]" class="form-control form-control-sm select2 pump-select"><option value="">Select</option>@foreach ($pumps as $pump)<option value="{{ $pump->id }}">{{ $pump->name }}</option>@endforeach</select></td>
                                    <td><input type="number" class="form-control form-control-sm totalamount" name="amount[]" readonly></td>
                                    <td class="text-center align-middle"><button type="button" class="btn btn-success btn-xs add-row"><i class="fas fa-plus"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
            <div class="card-footer text-right">
                <a href="{{ route('admin.programDetail', $data->id) }}" class="btn btn-default btn-sm">Cancel</a>
                <button type="submit" id="addBtn" class="btn btn-success btn-sm">
                    <i class="fas fa-save mr-1"></i> Save Challans
                </button>
                <div id="loader" class="d-none ml-2"><span class="spinner-border spinner-border-sm" role="status"></span> Saving...</div>
            </div>
            </form>
        </div>

    </div>
</section>
@endsection

@section('style')
<style>
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single { padding: 2px 6px; height: 31px !important; font-size: 13px; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 28px; }
</style>
@endsection

@section('script')
<script>
 $(document).ready(function() {
    // Unique key for this specific program
    const STORAGE_KEY = 'challan_form_{{ $data->id }}';

    // Initialize Select2
    $('.select2').select2({ allowClear: true, minimumResultsForSearch: 10 });

    // ==========================================
    // 1. LOCAL STORAGE: SAVE FUNCTION
    // ==========================================
    function saveToLocalStorage() {
        let rows = [];
        $('#programTable tbody tr').each(function() {
            rows.push({
                vendor_id: $(this).find('select[name="vendor_id[]"]').val() || '',
                truck_number: $(this).find('input[name="truck_number[]"]').val() || '',
                challan_no: $(this).find('input[name="challan_no[]"]').val() || '',
                cashamount: $(this).find('input[name="cashamount[]"]').val() || '',
                fuelqty: $(this).find('input[name="fuelqty[]"]').val() || '',
                fuel_rate: $(this).find('input[name="fuel_rate[]"]').val() || '',
                fuel_amount: $(this).find('input[name="fuel_amount[]"]').val() || '',
                fueltoken: $(this).find('input[name="fueltoken[]"]').val() || '',
                petrol_pump_id: $(this).find('select[name="petrol_pump_id[]"]').val() || '',
                amount: $(this).find('input[name="amount[]"]').val() || ''
            });
        });
        
        let formData = { newDate: $('#newDate').val(), rows: rows };
        localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
    }

    // ==========================================
    // 2. LOCAL STORAGE: RESTORE FUNCTION
    // ==========================================
    function restoreFromLocalStorage() {
        let savedData = localStorage.getItem(STORAGE_KEY);
        if (!savedData) return;

        try {
            let formData = JSON.parse(savedData);
            
            if (formData.newDate) {
                $('#newDate').val(formData.newDate);
            }
            
            if (formData.rows && formData.rows.length > 0) {
                $('#programTable tbody').empty(); // Remove default empty row
                
                formData.rows.forEach((row, index) => {
                    // Last row gets a "+" button, others get a "-" button
                    let isLast = (index === formData.rows.length - 1);
                    let actionBtn = isLast 
                        ? '<button type="button" class="btn btn-success btn-xs add-row"><i class="fas fa-plus"></i></button>'
                        : '<button type="button" class="btn btn-danger btn-xs remove-row"><i class="fas fa-trash"></i></button>';

                    // NOTE: No id="" attributes on inputs to prevent duplicate ID warnings
                    let newRow = `
                    <tr>
                        <td><select class="form-control form-control-sm select2 vendor-select" name="vendor_id[]"><option value="">Select</option>@foreach ($vendors as $vendor)<option value="{{ $vendor->id }}">{{ $vendor->name }}</option>@endforeach</select></td>
                        <td><input type="text" class="form-control form-control-sm" name="truck_number[]" value="${row.truck_number}"></td>
                        <td><input type="number" class="form-control form-control-sm" name="challan_no[]" value="${row.challan_no}"></td>
                        <td><input type="number" class="form-control form-control-sm cashamount" name="cashamount[]" value="${row.cashamount}"></td>
                        <td><input type="number" class="form-control form-control-sm fuelqty" name="fuelqty[]" value="${row.fuelqty}"></td>
                        <td><input type="number" class="form-control form-control-sm fuel_rate" name="fuel_rate[]" value="${row.fuel_rate || 115}"></td>
                        <td><input type="number" class="form-control form-control-sm fuel_amount" name="fuel_amount[]" value="${row.fuel_amount}" readonly></td>
                        <td><input type="number" class="form-control form-control-sm" name="fueltoken[]" value="${row.fueltoken}"></td>
                        <td><select name="petrol_pump_id[]" class="form-control form-control-sm select2 pump-select"><option value="">Select</option>@foreach ($pumps as $pump)<option value="{{ $pump->id }}">{{ $pump->name }}</option>@endforeach</select></td>
                        <td><input type="number" class="form-control form-control-sm totalamount" name="amount[]" value="${row.amount}" readonly></td>
                        <td class="text-center align-middle">${actionBtn}</td>
                    </tr>`;
                    
                    $('#programTable tbody').append(newRow);
                    
                    // Set Select2 values AFTER appending to DOM
                    let $currentRow = $('#programTable tbody tr:last');
                    $currentRow.find('select[name="vendor_id[]"]').val(row.vendor_id).trigger('change');
                    $currentRow.find('select[name="petrol_pump_id[]"]').val(row.petrol_pump_id).trigger('change');
                });

                // Re-initialize select2 for newly added elements
                $('.select2').select2({ allowClear: true, minimumResultsForSearch: 10 });
                
                // Notify user
                showFormAlert('<i class="fas fa-recycle mr-1"></i> Previous unsaved data has been automatically restored.', 'info');
            }
        } catch (e) {
            console.error("Error restoring from localStorage", e);
            localStorage.removeItem(STORAGE_KEY); // Clear corrupt data
        }
    }

    // Trigger restore on page load
    restoreFromLocalStorage();

    // ==========================================
    // 3. CALCULATION LOGIC
    // ==========================================
    function updateSummary() {
        $('#programTable tbody tr').each(function() {
            let fuelqty = parseFloat($(this).find('input.fuelqty').val()) || 0;
            let fuel_rate = parseFloat($(this).find('input.fuel_rate').val()) || 0;
            let cashamount = parseFloat($(this).find('input.cashamount').val()) || 0;

            let fuelTotal = (fuelqty * fuel_rate).toFixed(2);
            let grandTotal = (parseFloat(fuelTotal) + cashamount).toFixed(2);

            $(this).find('input.fuel_amount').val(fuelTotal);
            $(this).find('input.totalamount').val(grandTotal);
        });
        saveToLocalStorage(); // Save when math happens
    }

    // ==========================================
    // 4. ADD / REMOVE ROWS
    // ==========================================
    $(document).on('click', '.add-row', function() {
        // Change current last row's "+" to a "-"
        $('#programTable tbody tr:last .add-row').replaceWith('<button type="button" class="btn btn-danger btn-xs remove-row"><i class="fas fa-trash"></i></button>');

        let newRow = `
        <tr>
            <td><select class="form-control form-control-sm select2 vendor-select" name="vendor_id[]"><option value="">Select</option>@foreach ($vendors as $vendor)<option value="{{ $vendor->id }}">{{ $vendor->name }}</option>@endforeach</select></td>
            <td><input type="text" class="form-control form-control-sm" name="truck_number[]"></td>
            <td><input type="number" class="form-control form-control-sm" name="challan_no[]"></td>
            <td><input type="number" class="form-control form-control-sm cashamount" name="cashamount[]"></td>
            <td><input type="number" class="form-control form-control-sm fuelqty" name="fuelqty[]"></td>
            <td><input type="number" class="form-control form-control-sm fuel_rate" name="fuel_rate[]" value="115"></td>
            <td><input type="number" class="form-control form-control-sm fuel_amount" name="fuel_amount[]" readonly></td>
            <td><input type="number" class="form-control form-control-sm" name="fueltoken[]"></td>
            <td><select name="petrol_pump_id[]" class="form-control form-control-sm select2 pump-select"><option value="">Select</option>@foreach ($pumps as $pump)<option value="{{ $pump->id }}">{{ $pump->name }}</option>@endforeach</select></td>
            <td><input type="number" class="form-control form-control-sm totalamount" name="amount[]" readonly></td>
            <td class="text-center align-middle"><button type="button" class="btn btn-success btn-xs add-row"><i class="fas fa-plus"></i></button></td>
        </tr>`;
        
        $('#programTable tbody').append(newRow);
        $('.select2').select2({ allowClear: true, minimumResultsForSearch: 10 }); 
        saveToLocalStorage(); // Save when row added
    });

    $(document).on('click', '.remove-row', function() {
        if($('#programTable tbody tr').length > 1) {
            $(this).closest('tr').remove();
            
            // Make sure the new last row always has a "+" button
            $('#programTable tbody tr:last .remove-row').replaceWith('<button type="button" class="btn btn-success btn-xs add-row"><i class="fas fa-plus"></i></button>');
            
            updateSummary();
        } else {
            alert("You must have at least one row.");
        }
    });

    // ==========================================
    // 5. AUTO-SAVE ON ANY TYPING/DROPDOWN CHANGE
    // ==========================================
    $(document).on('input change', '#newDate, #programTable input, #programTable select', function(e) {
        // Prevent double-firing on select2, but ensures standard inputs save instantly
        if (!$(e.target).hasClass('select2-search__field')) {
            saveToLocalStorage();
        }
    });

    
    // Auto-calculate on input
    $(document).on('input', '#programTable input.fuelqty, #programTable input.fuel_rate, #programTable input.cashamount', updateSummary);


    // ==========================================
    // 6. FORM SUBMIT
    // ==========================================
    $('#createThisForm').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#addBtn');
        var originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving...').prop('disabled', true);
        $('#loader').show();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.status == 200) {
                    localStorage.removeItem(STORAGE_KEY);
                    showToast(response.message || 'Challans added successfully!', 'success');
                } else {
                    showFormAlert(response.message || 'Error occurred.', 'danger');
                }
            },
            error: function(xhr) {
                // SAFELY HANDLE ERRORS (Fixes "Cannot read properties of undefined" bug)
                let errorMsg = 'An unknown error occurred.';
                
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                } else {
                    // If it's a 403 Forbidden or generic HTML error page
                    errorMsg = `Server Error (${xhr.status}): Please check your permissions or refresh the page.`;
                }
                
                showFormAlert(errorMsg, 'danger');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
                $('#loader').hide();
            }
        });
    });

    // ==========================================
    // ALERT HELPERS
    // ==========================================
    function showFormAlert(message, type) {
        $('#form-alert-container').html(`<div class="alert alert-${type} alert-dismissible fade show py-2" role="alert">${message}<button type="button" class="close" data-dismiss="alert">&times;</button></div>`);
        setTimeout(() => { $('#form-alert-container .alert').alert('close'); }, 7000);
    }
    
    function showToast(message, type) {
        var bg = type === 'success' ? 'bg-success' : 'bg-danger';
        $('body').append(`<div class="toast-container position-fixed top-0 right-0 p-3" style="z-index:9999;"><div class="toast show ${bg} text-white" role="alert"><div class="toast-header ${bg} text-white border-0"><strong class="mr-auto">Success</strong><button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button></div><div class="toast-body">${message}</div></div></div>`);
        setTimeout(() => { $('.toast-container').remove(); }, 4000);
    }
});
</script>
@endsection