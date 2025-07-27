@extends('admin.layouts.admin')

@section('content')

<style>
    .form-checkbox {
        font-family: system-ui, sans-serif;
        font-size: 2rem;
        font-weight: bold;
        line-height: 1.1;
        display: grid;
        grid-template-columns: 1em auto;
        gap: 0.5em;
    }

    .custom-checkbox {
        height: 30px;
    }
</style>

<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
        <div class="row">
            <div class="col-2">
                <a href="{{ route('admin.programDetail', $programId) }}" class="btn btn-secondary my-3">Back</a>
            </div>
        </div>
    </div>
</section>
<!-- /.content -->

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Vendor Ledger</h3>
                        <div class="card-tools">
                            <a href="{{ route('export.template') }}" class="btn btn-tool">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- @if (session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif --}}
                        <form action="{{ route('billGeneratingStore') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="upload">Uploads</label>
                                            <input type="file" name="file" required>
                                            <input type="hidden" name="programId" value="{{ $programId }}" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Action</label><br>
                                            <button type="submit" class="btn btn-secondary">Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Update old qty for test</h3>
                        <div class="card-tools">
                            <a href="{{ route('export.programDetails', $programId) }}" class="btn btn-tool">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- @if (session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif --}}
                        <form action="{{ route('updateOldQty') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="upload">Uploads</label>
                                            <input type="file" name="file" required>
                                            <input type="hidden" name="programId" value="{{ $programId }}" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Action</label><br>
                                            <button type="submit" class="btn btn-secondary">Upload</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Mother Vessel: {{ $data->motherVassel->name }}</h3>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div style="overflow-x:auto;">
                            <!-- Destination Filter -->
                            <div class="col-md-4 mb-3">
                                <label for="destinationFilter"><strong>Filter by Destination:</strong></label>
                                <select id="destinationFilter" class="form-control select2">
                                    <option value="">-- All Destinations --</option>
                                    @php
                                        $uniqueDestinations = collect($data->programDetail)
                                            ->pluck('destination.name')
                                            ->filter()
                                            ->unique()
                                            ->sort()
                                            ->values();
                                    @endphp
                                    @foreach ($uniqueDestinations as $dest)
                                        <option value="{{ $dest }}">{{ $dest }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Table -->
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Bill Status</th>
                                        <th>Bill No</th>
                                        <th>Date</th>
                                        <th>Vendor</th>
                                        <th>Header ID</th>
                                        <th>Truck Number</th>
                                        <th>Challan no</th>
                                        <th>Destination</th>
                                        <th>Qty</th>
                                        <th>Carrying Bill</th>
                                        <th>Advance</th>
                                        <th>Fuel qty</th>
                                        <th>Fuel token</th>
                                        <th>Fuel Amount</th>
                                        <th>Pump name</th>
                                        <th>Line Charge</th>
                                        <th>Scale fee</th>
                                        <th>Other Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalfuelqty = 0;
                                        $totalcarrying_bill = 0;
                                        $totaladvance = 0;
                                        $totalother_cost = 0;
                                        $totalscale_fee = 0;
                                        $totalline_charge = 0;
                                        $totaldest_qty = 0;
                                    @endphp
                                    @foreach ($data->programDetail as $key => $data)
                                        <tr class="{{ $data->generate_bill == 1 ? 'table-warning' : '' }}" data-id="{{ $data->id }}">
                                            <td style="text-align: center">{{ $key + 1 }}</td>
                                            <td style="text-align: center">
                                                <div style="display: flex; align-items: center; gap: 6px; justify-content: center">
                                                    @if ($data->generate_bill == 1)
                                                        <form action="{{ route('generateBill.undo', $data->id) }}" method="POST" onsubmit="return confirm('Uncheck this item?')">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-undo"></i></button>
                                                        </form>
                                                    @endif

                                                <label class="form-checkbox grid layout mb-0">
                                                    <input type="checkbox" class="custom-checkbox generate-bill-checkbox"
                                                          name="checkbox-checked"
                                                          data-program-detail-id="{{ $data->id }}"
                                                          data-dest-qty="{{ $data->dest_qty }}"
                                                          @if ($data->generate_bill == 1) checked disabled @endif />
                                                </label>
                                                </div>
                                            </td>

                                            @php
                                                $fuelBills = $data->advancePayment->petrolPump ?? ''
                                                    ? \App\Models\FuelBill::with('petrolPump:id,name')
                                                        ->where('petrol_pump_id', $data->advancePayment->petrolPump->id)
                                                        ->get(['id', 'unique_id', 'qty', 'bill_number', 'petrol_pump_id'])
                                                    : collect();
                                            @endphp
                                            <td style="text-align: center">{{ $data->bill_no }}</td>
                                            <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                                            <td style="text-align: center">{{ $data->vendor->name }}</td>
                                            <td style="text-align: center">{{ $data->headerid }}</td>
                                            <td style="text-align: center">{{ strtoupper($data->truck_number) }}</td>
                                            <td style="text-align: center">{{ $data->challan_no }}</td>
                                            <td style="text-align: center">{{ $data->destination->name ?? ' ' }}</td>
                                            <td style="text-align: center">{{ $data->dest_qty }}
                                                @if ($data->old_qty)
                                                    <span class="badge badge-info" >{{ $data->old_qty }}</span>
                                                    
                                                @endif
                                            </td>
                                            <td style="text-align: center">{{ $data->carrying_bill }}</td>
                                            <td style="text-align: center">{{ $data->advancePayment->cashamount ?? '' }}</td>
                                            <td style="text-align: center">{{ $data->advancePayment->fuelqty ?? '' }}</td>
                                            <td style="text-align: center">{{ $data->advancePayment->fueltoken ?? '' }}</td>
                                            <td style="text-align: center">{{ $data->advancePayment->fuelamount ?? '' }}</td>
                                            <td style="text-align: center">{{ $data->advancePayment->petrolPump->name ?? '' }}</td>
                                            <td style="text-align: center">{{ $data->line_charge }}</td>
                                            <td style="text-align: center">{{ $data->scale_fee }}</td>
                                            <td style="text-align: center">{{ $data->other_cost }}</td>
                                            @php
                                                $totalfuelqty += $data->advancePayment->fuelqty ?? 0;
                                                $totalcarrying_bill += $data->carrying_bill ?? 0;
                                                $totaladvance += $data->advance ?? 0;
                                                $totalother_cost += $data->other_cost ?? 0;
                                                $totalscale_fee += $data->scale_fee ?? 0;
                                                $totalline_charge += $data->line_charge ?? 0;
                                                $totaldest_qty += $data->dest_qty ?? 0;
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center" colspan="2"><small>Total qty: </small>{{ $totaldest_qty }}</td>
                                        <td style="text-align: center" colspan="2"><small>Carrying Bill: </small>{{ $totalcarrying_bill }}</td>
                                        <td style="text-align: center" colspan="2"><small>Total Advance:</small>{{ $totaladvance }}</td>
                                        <td style="text-align: center"><small>Fuel qty: </small>{{ $totalfuelqty }}</td>
                                        <td style="text-align: center"><small>Line Charge: </small>{{ $totalline_charge }}</td>
                                        <td style="text-align: center"><small>Scale fee: </small>{{ $totalscale_fee }}</td>
                                        <td style="text-align: center"><small>Other Cost: </small>{{ $totalother_cost }}</td>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center"></td>
                                        <td style="text-align: center"></td>
                                    </tr>
                                </tfoot>
                            </table>

                            <!-- Form moved outside the table -->
                            <div id="pump-form-row" style="display: none; margin-top: 20px;">
                                <div class="row">
                                    <div class="col-2">
                                        <span style="margin-right: 10px; font-weight: bold;">
                                            Selected: <span id="total-selected">0</span> |
                                            Total Qty: <span id="total-dest-qty">0</span>
                                        </span>
                                    </div>
                                    <div class="col-10">
                                        <form id="pump-action-form" action="{{ route('bill.generate') }}" method="POST" style="display: flex; justify-content: center; align-items: center;">
                                            @csrf
                                            <input type="hidden" name="selected_ids" id="selected_ids">
                                            <input type="text" name="bill_no" id="bill_no-display" class="form-control" placeholder="Enter Bill No" style="width: 350px; margin-right: 10px;" required>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-check-circle"></i> Submit Bill
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
    $(document).ready(function () {
        const programId = @json($programId);
        const storageKey = `selectedBillRows_${programId}`;

        let selectedBillRows = JSON.parse(localStorage.getItem(storageKey)) || {};
        // console.log(selectedBillRows);
        // Initialize checkbox states based on localStorage
        $('.generate-bill-checkbox').each(function () {
            const id = $(this).data('program-detail-id');
            const row = $(this).closest('tr');
            const qty = parseFloat($(this).data('dest-qty')) || 0;

            if (selectedBillRows.hasOwnProperty(id)) {
                if (!this.disabled) {
                    $(this).prop('checked', true);
                    row.addClass('table-success');
                }
            }
        });

        // Update form state on page load
        updateFormState();

        // Filter by destination
        $('#destinationFilter').on('change', function () {
            const value = $(this).val();
            $('#example1').DataTable().column(8).search(value).draw();
        });

        // Handle checkbox changes
        $('.generate-bill-checkbox').on('change', function () {
            const id = $(this).data('program-detail-id');
            const qty = parseFloat($(this).data('dest-qty')) || 0;
            const row = $(this).closest('tr');
            const rowDataId = row.data('id');

            // console.log(id, qty, rowDataId);

            if (this.checked && !this.disabled) {
                selectedBillRows[rowDataId] = qty;
                row.addClass('table-success');
            } else {
                delete selectedBillRows[rowDataId];
                row.removeClass('table-success');
            }

            localStorage.setItem(storageKey, JSON.stringify(selectedBillRows));
            updateFormState();
        });

        // Update form visibility and values
        function updateFormState() {
            const selectedIds = Object.keys(selectedBillRows);
            const totalQty = Object.values(selectedBillRows).reduce((a, b) => a + b, 0);

            if (selectedIds.length) {
                $('#pump-form-row').show();
                $('#selected_ids').val(JSON.stringify(selectedIds));
                $('#total-dest-qty').text(totalQty.toFixed(2));
                $('#total-selected').text(selectedIds.length); 
            } else {
                $('#pump-form-row').hide();
                $('#selected_ids').val('');
                $('#bill_no-display').val('');
                $('#total-dest-qty').text(0);
                $('#total-selected').text(0);
            }
        }

        // Clear localStorage on form submission
        $('#pump-action-form').on('submit', function () {
            localStorage.removeItem(storageKey);
        });

        // Initialize DataTable
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 10,
            "buttons": [
                "copy",
                "csv",
                "excel",
                {
                    extend: 'pdf',
                    customize: function (doc) {
                        doc.content.splice(0, 0, {
                            text: 'Program details',
                            style: 'header',
                            alignment: 'center'
                        });
                    },
                    filename: 'Program_Details'
                },
                "print"
            ],
            "lengthMenu": [[20, 100, -1, 50, 25], [20, 100, "All", 50, 25]]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    // Auto-remove alerts after 3 seconds
    setTimeout(function () {
        let alert = document.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 3000);
</script>
@endsection