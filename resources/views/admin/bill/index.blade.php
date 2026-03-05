@extends('admin.layouts.admin')

@section('content')
<style>
    .card-summary { background: #f8f9fa; border-left: 4px solid #17a2b8; }
    .table-vcenter td, .table-vcenter th { vertical-align: middle; }
    .net-amount-box { font-size: 1.5rem; font-weight: bold; color: #28a745; }
    .form-control:read-only { background-color: #e9ecef; opacity: 1; }
    /* Spinner for search */
    .spinner-border-sm { display: none; }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Bill Settlement</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-search mr-1"></i> Find Bill</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-5">
                                <label>Client</label>
                                <select id="client_id" class="form-control select2" style="width: 100%;">
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7">
                                <label>Bill Number</label>
                                <div class="input-group">
                                    <input type="text" id="bill_number" class="form-control" placeholder="Enter Bill No...">
                                    <div class="input-group-append">
                                        <button id="searchBtn" class="btn btn-primary">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                        
                        <div class="row">
                            <div class="col-md-12">
                                <label>Upload Excel file</label>
                                <div class="input-group">
                                    <input type="file" id="document" class="form-control" accept=".xlsx, .xls">
                                    <div class="input-group-append">
                                        <button id="uploadBtn" class="btn btn-primary">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            <i class="fas fa-file"></i> Upload
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <div class="card card-secondary" id="resultCard" style="display:none;">
                    <div class="card-header">
                        <h3 class="card-title">Challan Details for: <span id="displayBillNo" class="badge badge-light"></span></h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm table-hover table-striped table-vcenter mb-0" id="prgmDtl">
                            <thead class="text-center">
                                <tr>
                                    <th>Sl</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Vendor</th>
                                    <th>Challan No</th>
                                    <th>Header ID</th>
                                    <th>From - To</th>
                                    <th>Scale Fee</th>
                                    <th>Prev. Qty</th>
                                    <th>Prev. Amount</th>
                                    <th>Qty</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="billTableBody">
                                </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <th colspan="7" class="text-right">Grand Total:</th>
                                    <th class="text-center" id="totalScaleFee">0.00</th>
                                    <th class="text-center" id="footerPevQty">0</th>
                                    <th class="text-center text-success" id="footerPevTotal">0.00</th>
                                    <th class="text-center" id="footerQty">0</th>
                                    <th class="text-center text-success" id="footerTotal">0.00</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card card-info sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-1"></i> Receive Payment</h3>
                    </div>
                    <form id="billForm">
                        <div class="card-body">
                            <div class="billmsg"></div>
                            
                            <div class="form-group">
                                <label>Receiving Date</label>
                                <input type="date" class="form-control" name="date" value="{{date('Y-m-d')}}">
                            </div>

                            <div class="form-group">
                                <label>Payment Method</label>
                                <select name="rcvType" class="form-control">
                                    <option value="Bank">Bank</option>
                                    <option value="Cash">Cash</option>
                                </select>
                            </div>

                            <hr>

                            <div class="row">
                                <div class="col-6">
                                    <label>Total Qty</label>
                                    <input type="number" class="form-control" id="totalqty" name="totalqty" readonly>
                                </div>
                                <div class="col-6">
                                    <label>Bill Amount</label>
                                    <input type="number" class="form-control" id="totalAmount" name="totalAmount" readonly>
                                </div>
                            </div>

                            <div class="form-group mt-2">
                                <label class="text-danger small">Less: Maintenance & Other Costs</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend"><span class="input-group-text">M</span></div>
                                    <input type="number" class="form-control calc" id="maintainance" name="maintainance" placeholder="Maintenance">
                                    <input type="number" class="form-control calc" id="otherexp" name="otherexp" placeholder="Others">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="text-success small">Add: Scale & Other Income</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">S</span></div>
                                    <input type="number" class="form-control calc" id="scaleCharge" name="scaleCharge" placeholder="Scale">
                                    <input type="number" class="form-control calc" id="otherRcv" name="otherRcv" placeholder="Others">
                                </div>
                            </div>

                            <div class="card card-summary mt-3 p-2 text-center">
                                <label class="mb-0">Net Amount to Receive</label>
                                <div class="net-amount-box">
                                    BDT <span id="netAmountDisplay">0.00</span>
                                </div>
                                <input type="hidden" id="netAmount" name="netAmount">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="saveBtn" class="btn btn-block btn-info btn-lg">
                                <i class="fas fa-check-circle mr-1"></i> Confirm & Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection




@section('script')
<script>
$(document).ready(function() {
    // Initialize Select2 if available
    $('.select2').select2();

    // 1. Search Logic
    $('#searchBtn').click(function() {
        const billNo = $('#bill_number').val();
        const clientId = $('#client_id').val();

        if (!billNo) {
            Swal.fire('Error', 'Please enter a Bill Number', 'error');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).find('.spinner-border').show();

        $.ajax({
            url: "{{ route('admin.checkBill') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                bill_number: billNo,
                client_id: clientId
            },
            success: function(response) {
                if (response.status === 200) {
                    // Update Table
                    if ($.fn.DataTable.isDataTable('#prgmDtl')) {
                        $('#prgmDtl').DataTable().destroy();
                    }
                    
                    $('#billTableBody').html(response.html);
                    $('#displayBillNo').text(billNo);
                    
                    // Update Summary Inputs
                    $('#totalqty').val(response.totalQty);
                    $('#totalAmount').val(response.totalAmount);
                    
                    // Update Footer
                    $('#footerQty').text(response.totalQty);
                    $('#footerTotal').text(response.totalAmount);

                    calculateNetAmount(); // Initial calc
                    $('#resultCard').fadeIn();
                    
                    // Re-init DataTable
                    $('#prgmDtl').DataTable({ "paging": false, "searching": false, "info": false });
                } else {
                    alert(response.message);
                }
            },
            complete: function() {
                $btn.prop('disabled', false).find('.spinner-border').hide();
            }
        });
    });

    // 2. Calculation Logic (One single listener)
    $(document).on('keyup change', '.calc', function() {
        calculateNetAmount();
    });

    function calculateNetAmount() {
        let base = parseFloat($('#totalAmount').val()) || 0;
        let main = parseFloat($('#maintainance').val()) || 0;
        let othe = parseFloat($('#otherexp').val()) || 0;
        let scal = parseFloat($('#scaleCharge').val()) || 0;
        let othr = parseFloat($('#otherRcv').val()) || 0;

        let net = (base + scal + othr) - (main + othe);
        
        $('#netAmount').val(net.toFixed(2));
        $('#netAmountDisplay').text(net.toLocaleString(undefined, {minimumFractionDigits: 2}));
    }

    // 3. Save Logic
    $('#billForm').on('submit', function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        formData.append("client_id", $('#client_id').val());
        formData.append("bill_number", $('#bill_number').val());

        let $btn = $('#saveBtn');
        $btn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: '{{ route("admin.billStore") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                $(".billmsg").html(`<div class="alert alert-success">${res.message}</div>`);
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text('Confirm & Save');
                let msg = xhr.responseJSON ? xhr.responseJSON.message : 'Error saving bill';
                $(".billmsg").html(`<div class="alert alert-danger">${msg}</div>`);
            }
        });
    });
});
</script>
@endsection