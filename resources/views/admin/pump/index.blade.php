@extends('admin.layouts.admin')

@section('content')

<!-- Summary Cards Section -->
<section class="content">
    <div class="container-fluid">
        <div class="row">

            <!-- Total Pumps -->
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalPumps }}</h3>
                        <p>Total Petrol Pumps</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-gas-pump"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Total Fuel Bills -->
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalFuelBills }}</h3>
                        <p>Total Fuel Bills</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <!-- Total Invoice Qty -->
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ number_format($totalInvoiceQty, 2) }}</h3>
                        <p>Total Invoice Quantity</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <div class="small-box-footer" style="background: rgba(0,0,0,0.1);">
                        Marked: <b>{{ number_format($totalMarkQty, 2) }}</b> | 
                        Pending: <b>{{ number_format($totalNotMarkQty, 2) }}</b>
                    </div>
                </div>
            </div>

            <!-- Total Due / Payable -->
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="small-box {{ $grandDue >= 0 ? 'bg-danger' : 'bg-primary' }}">
                    <div class="inner">
                        <h3>৳ {{ number_format(abs($grandDue), 2) }}</h3>
                        <p>{{ $grandDue >= 0 ? 'Total Payable' : 'Total Receivable' }}</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="small-box-footer" style="background: rgba(0,0,0,0.1);">
                        Status: <b>{{ $grandDue >= 0 ? 'Due' : 'Advance' }}</b>
                    </div>
                </div>
            </div>

        </div>

        <!-- Secondary Stats Row -->
        <div class="row">
            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Marked Qty</span>
                        <span class="info-box-number">{{ number_format($totalMarkQty, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pending Qty</span>
                        <span class="info-box-number">{{ number_format($totalNotMarkQty, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-truck"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Avg Bills / Pump</span>
                        <span class="info-box-number">
                            {{ $totalPumps > 0 ? number_format($totalFuelBills / $totalPumps, 1) : 0 }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-percentage"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Marked Progress</span>
                        <div class="progress">
                            <div class="progress-bar bg-success" 
                                 style="width: {{ $totalInvoiceQty > 0 ? round(($totalMarkQty/$totalInvoiceQty)*100, 1) : 0 }}%"></div>
                        </div>
                        <span class="progress-description">
                            {{ $totalInvoiceQty > 0 ? round(($totalMarkQty/$totalInvoiceQty)*100, 1) : 0 }}% Completed
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Add New Button -->
<section class="content mt-2" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <button type="button" class="btn btn-secondary mb-3" id="newBtn">
                    <i class="fas fa-plus"></i> Add New Pump
                </button>
            </div>
        </div>
    </div>
</section>


<!-- Add New Form -->
<section class="content" id="addThisFormContainer" style="display:none;">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-8">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-gas-pump"></i> Add New Petrol Pump</h3>
                    </div>
                    <div class="card-body">
                        <div class="ermsg"></div>
                        <form id="createThisForm">
                            @csrf
                            <input type="hidden" class="form-control" id="codeid" name="codeid">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <input type="text" class="form-control" id="location" name="location">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">
                            <i class="fas fa-save"></i> Create
                        </button>
                        <button type="submit" id="FormCloseBtn" class="btn btn-default">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Pumps Table -->
<!-- ADDED id="contentContainer" HERE TO FIX BUTTONS -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-list"></i> All Petrol Pumps & Summary</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table id="example1" class="table table-bordered table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width: 50px;">Sl</th>
                                    <th>Pump Name</th>
                                    <th>Location</th>
                                    <th class="text-center">Bills</th>
                                    <th class="text-center">Invoice Qty</th>
                                    <th class="text-center">Marked Qty</th>
                                    <th class="text-center">Pending Qty</th>
                                    <th class="text-center">Due / Advance</th>
                                    <th class="text-center">Last Bill</th>
                                    <th class="text-center" style="width: 120px;">Action</th>
                                    <th class="text-center">Ledger</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $data)
                                    @php
                                        $stats = $pumpStats[$data->id] ?? [
                                            'bill_count' => 0,
                                            'invoice_qty' => 0,
                                            'mark_qty' => 0,
                                            'notmark_qty' => 0,
                                            'due' => 0,
                                            'last_bill' => null,
                                        ];
                                        $due = $stats['due'];
                                        $dueClass = $due >= 0 ? 'badge-danger' : 'badge-success';
                                        $dueLabel = $due >= 0 ? 'Payable' : 'Receivable';
                                    @endphp
                                    <tr>
                                        <td class="text-center font-weight-bold">{{ $key + 1 }}</td>
                                        <td>
                                            <i class="fas fa-gas-pump text-info mr-1"></i>
                                            <strong>{{ $data->name }}</strong>
                                        </td>
                                        <td>
                                            @if($data->location)
                                                <i class="fas fa-map-marker-alt text-muted mr-1"></i>
                                                {{ $data->location }}
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-info badge-pill">
                                                {{ $stats['bill_count'] }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ number_format($stats['invoice_qty'], 2) }}</td>
                                        <td class="text-center">
                                            <span class="text-success font-weight-bold">
                                                {{ number_format($stats['mark_qty'], 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="text-danger font-weight-bold">
                                                {{ number_format($stats['notmark_qty'], 2) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $dueClass }} badge-pill">
                                                ৳ {{ number_format(abs($due), 2) }}
                                            </span>
                                            <div class="small text-muted">{{ $dueLabel }}</div>
                                        </td>
                                        <td class="text-center">
                                            @if($stats['last_bill'])
                                                <span class="small">{{ \Carbon\Carbon::parse($stats['last_bill'])->format('d M, Y') }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <span class="btn btn-success btn-xs add-sq-btn" 
                                                      style="cursor: pointer;" 
                                                      data-id="{{ $data->id }}" 
                                                      title="Add Bill">
                                                    <i class="fas fa-plus"></i>
                                                </span>
                                                <span class="btn btn-info btn-xs view-btn" 
                                                      style="cursor: pointer;" 
                                                      data-id="{{ $data->id }}" 
                                                      title="View Bills">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                                <a id="EditBtn" class="btn btn-primary btn-xs"
                                                   rid="{{ $data->id }}" 
                                                   style="cursor:pointer" 
                                                   title="Edit">
                                                    <i class="fa fa-edit" style="font-size:16px;"></i>
                                                </a>
                                                
                                                {{-- Delete Button Hidden as requested --}}
                                                {{-- 
                                                <a id="deleteBtn" rid="{{ $data->id }}" style="cursor:pointer" title="Delete">
                                                    <i class="fa fa-trash-o text-danger" style="font-size:16px;"></i>
                                                </a> 
                                                --}}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.pump.ledger', $data->id) }}" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="fas fa-book"></i> Ledger
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="thead-dark">
                                <tr>
                                    <th colspan="3" class="text-right">Total:</th>
                                    <th class="text-center">{{ $totalFuelBills }}</th>
                                    <th class="text-center">{{ number_format($totalInvoiceQty, 2) }}</th>
                                    <th class="text-center">{{ number_format($totalMarkQty, 2) }}</th>
                                    <th class="text-center">{{ number_format($totalNotMarkQty, 2) }}</th>
                                    <th class="text-center">৳ {{ number_format(abs($grandDue), 2) }}</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Add Sequence (Fuel Bill) Modal -->
<div class="modal fade" id="payModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-secondary">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add Fuel Bill</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="payForm">
                <div class="modal-body">
                    <div class="permsg"></div>
                    <div class="form-group">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                    </div>
                    <div class="form-group">
                        <label>Fuel Bill Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="bill_number" name="bill_number">
                    </div>
                    <div class="form-group">
                        <label>Invoice Qty <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="invqty" name="invqty" step="0.01">
                    </div>
                    <div class="form-group">
                        <label>Total Vehicle <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="vehicle_count" name="vehicle_count">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Create</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- View Bills Modal -->
<div class="modal fade" id="tranModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Fuel Bill Numbers & Quantity</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <table id="trantable" class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Date</th>
                            <th>Bill Number</th>
                            <th>Total Vehicle</th>
                            <th>Fuel Qty</th>
                            <th>Fuel Amount</th>
                            <th>Unique ID</th>
                            <th>Edit</th>
                            
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Edit Full Modal -->
<div class="modal fade" id="editFullModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="updateFullForm" method="POST" action="{{ route('admin.pump.update') }}">
            @csrf
            <input type="hidden" name="tran_id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Sequence Entry</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" id="edit_date" required>
                    </div>
                    <div class="form-group">
                        <label>Bill Number</label>
                        <input type="text" class="form-control" name="bill_number" id="edit_bill_number" required>
                    </div>
                    <div class="form-group">
                        <label>Invoice Qty</label>
                        <input type="number" class="form-control" name="qty" id="edit_qty" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Total Vehicle</label>
                        <input type="number" class="form-control" name="vehicle_count" id="edit_vehicle_count" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    $(function () {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "order": [[0, "asc"]]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    // Edit Full Modal populate
    $(document).on('click', '.editFullBtn', function () {
        $('#edit_id').val($(this).data('id'));
        $('#edit_date').val($(this).data('date'));
        $('#edit_bill_number').val($(this).data('bill_number'));
        $('#edit_qty').val($(this).data('qty'));
        $('#edit_vehicle_count').val($(this).data('vehicle_count'));
        $('#editFullModal').modal('show');
    });

    // Update full form
    $(document).on('submit', '#updateFullForm', function (e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status === 200) {
                    alert('Update successful!');
                    $('#editFullModal').modal('hide');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert('Update failed!');
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>

<script>
    $(document).ready(function () {
        $("#addThisFormContainer").hide();

        $("#newBtn").click(function () {
            clearform();
            $("#newBtn").hide(100);
            $("#addThisFormContainer").show(300);
            $('html, body').animate({ scrollTop: $("#addThisFormContainer").offset().top - 80 }, 300);
        });

        $("#FormCloseBtn").click(function (e) {
            e.preventDefault();
            $("#addThisFormContainer").hide(200);
            $("#newBtn").show(100);
            clearform();
        });

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        var url = "{{ URL::to('/admin/pump') }}";
        var upurl = "{{ URL::to('/admin/pump-update') }}";

        // Add / Update
        $("#addBtn").click(function (e) {
            e.preventDefault();
            if ($(this).val() == 'Create') {
                var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("location", $("#location").val());
                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                        } else if (d.status == 300) {
                            $(".ermsg").html(d.message);
                            setTimeout(() => location.reload(), 2000);
                        }
                    },
                    error: function (d) { console.log(d); }
                });
            }

            if ($(this).val() == 'Update') {
                var form_data = new FormData();
                form_data.append("name", $("#name").val());
                form_data.append("location", $("#location").val());
                form_data.append("codeid", $("#codeid").val());
                $.ajax({
                    url: upurl,
                    type: "POST",
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (d) {
                        if (d.status == 303) {
                            $(".ermsg").html(d.message);
                        } else if (d.status == 300) {
                            $(".ermsg").html(d.message);
                            setTimeout(() => location.reload(), 2000);
                        }
                    },
                    error: function (d) { console.log(d); }
                });
            }
        });

        // Edit
        $("#contentContainer").on('click', '#EditBtn', function () {
            codeid = $(this).attr('rid');
            info_url = url + '/' + codeid + '/edit';
            $.get(info_url, {}, function (d) {
                populateForm(d);
                $('html, body').animate({ scrollTop: $("#addThisFormContainer").offset().top - 80 }, 300);
            });
        });

        // Delete (Hidden in HTML, but keeping JS in case you want to re-enable it later)
        $("#contentContainer").on('click', '#deleteBtn', function () {
            if (!confirm('Are you sure you want to delete this pump?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/' + codeid;
            $.ajax({
                url: info_url,
                method: "GET",
                type: "DELETE",
                data: {},
                success: function (d) {
                    if (d.success) {
                        alert(d.message);
                        location.reload();
                    }
                },
                error: function (d) { console.log(d); }
            });
        });

        function populateForm(data) {
            $("#name").val(data.name);
            $("#location").val(data.location);
            $("#codeid").val(data.id);
            $("#addBtn").val('Update');
            $("#addBtn").html('<i class="fas fa-save"></i> Update');
            $("#addThisFormContainer").show(300);
            $("#newBtn").hide(100);
        }

        function clearform() {
            $('#createThisForm')[0].reset();
            $("#codeid").val('');
            $("#addBtn").val('Create');
            $("#addBtn").html('<i class="fas fa-save"></i> Create');
            $(".ermsg").html('');
        }

        // Add Fuel Bill (+ button)
        $("#contentContainer").on('click', '.add-sq-btn', function () {
            var id = $(this).data('id');
            $('#payModal').modal('show');
            $('#payForm').off('submit').on('submit', function (event) {
                event.preventDefault();
                var form_data = new FormData();
                form_data.append("pumpId", id);
                form_data.append("date", $("#date").val());
                form_data.append("bill_number", $("#bill_number").val());
                form_data.append("invqty", $("#invqty").val());
                form_data.append("vehicle_count", $("#vehicle_count").val());

                if (!$("#bill_number").val()) { alert('Please enter bill number.'); return; }
                if (!$("#invqty").val()) { alert('Please enter quantity.'); return; }
                if (!$("#vehicle_count").val()) { alert('Please enter total vehicle.'); return; }

                $.ajax({
                    url: '{{ URL::to('/admin/add-fuel-bill-number') }}',
                    method: 'POST',
                    data: form_data,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.status == 303) {
                            $(".permsg").html(response.message);
                        } else if (response.status == 300) {
                            $(".permsg").html(response.message);
                            setTimeout(() => location.reload(), 2000);
                            $('#payModal').modal('hide');
                        }
                    },
                    error: function (xhr) { console.log(xhr.responseText); }
                });
            });
        });

        $('#payModal').on('hidden.bs.modal', function () {
            $('#payForm')[0].reset();
            $('#date').val('{{ \Carbon\Carbon::now()->format("Y-m-d") }}');
            $(".permsg").html('');
        });

        // View Bills (eye button)
        var viewRequestCounter = 0;
        var isViewLoading = false;

        $("#contentContainer").on('click', '.view-btn', function () {
            if (isViewLoading) return;
            var id = $(this).data('id');
            var currentRequestId = ++viewRequestCounter;

            $('#trantable tbody').html(`
                <tr><td colspan="7" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 mb-0 text-muted">Loading data...</p>
                </td></tr>
            `);
            $('#tranModal').modal('show');
            isViewLoading = true;
            $('.view-btn').addClass('disabled').css('pointer-events', 'none');

            var form_data = new FormData();
            form_data.append("pumpId", id);

            $.ajax({
                url: '{{ URL::to("/admin/get-petrol-pump-bill") }}',
                method: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (currentRequestId === viewRequestCounter) {
                        if (response.status == 300) {
                            $('#trantable tbody').html(response.data);
                        } else {
                            $('#trantable tbody').html(`<tr><td colspan="7" class="text-center text-danger py-3">Error loading data</td></tr>`);
                        }
                    }
                    isViewLoading = false;
                    $('.view-btn').removeClass('disabled').css('pointer-events', 'auto');
                },
                error: function (xhr) {
                    if (currentRequestId === viewRequestCounter) {
                        $('#trantable tbody').html(`<tr><td colspan="7" class="text-center text-danger py-3">Failed to load data. Please try again.</td></tr>`);
                    }
                    isViewLoading = false;
                    $('.view-btn').removeClass('disabled').css('pointer-events', 'auto');
                }
            });
        });

        $('#tranModal').on('hidden.bs.modal', function () {
            $('#trantable tbody').empty();
        });
    });
</script>
@endsection