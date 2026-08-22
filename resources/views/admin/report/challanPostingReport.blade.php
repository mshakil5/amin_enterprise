@extends('admin.layouts.admin')

@section('content')

@php
    // Safely get permissions whether they are a JSON string or already an array
    $permissions = auth()->user()->role->permission ?? [];
    if (is_string($permissions)) {
        $permissions = json_decode($permissions, true) ?? [];
    }
@endphp

<style>
    .summary-card .small-box {
        border-radius: 0.5rem;
        overflow: hidden;
        transition: transform 0.2s ease;
    }
    .summary-card .small-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    }
    .table td, .table th {
        vertical-align: middle;
        white-space: nowrap;
    }
    .table-actions form { display: inline; }
</style>

<!-- Back Button -->
<section class="content mt-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <a href="{{ route('challanPostingVendorReport') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>&nbsp;Back
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Summary Cards -->
<section class="content">
    <div class="container-fluid">
        <div class="row summary-card">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h4>{{ $motherVesselName }}</h4>
                        <p>{{ $vendor->name }}</p>
                    </div>
                    <div class="icon"><i class="fas fa-ship"></i></div>
                    <a href="#" class="small-box-footer">Vendor Balance: ৳{{ number_format($summary['vendor_balance'], 2) }}</a>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>৳{{ number_format($summary['total_carrying_bill'], 2) }}</h3>
                        <p>Total Carrying Bill</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="small-box-footer">Records: {{ $summary['record_count'] }}</div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>৳{{ number_format($summary['total_advance'], 2) }}</h3>
                        <p>Total Advance</p>
                    </div>
                    <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="small-box-footer">Fuel Qty: {{ $summary['total_fuel_qty'] }} L</div>
                </div>
            </div>

            <div class="col-lg-3 col-sm-6 col-12">
                <div class="small-box bg-{{ $summary['total_due'] >= 0 ? 'danger' : 'primary' }}">
                    <div class="inner">
                        <h3>৳{{ number_format(abs($summary['total_due']), 2) }}</h3>
                        <p>{{ $summary['total_due'] >= 0 ? 'Total Due' : 'Advance Excess' }}</p>
                    </div>
                    <div class="icon"><i class="fas fa-balance-scale"></i></div>
                    <div class="small-box-footer">Paid: ৳{{ number_format($summary['due_payment'], 2) }}</div>
                </div>
            </div>
        </div>

        <!-- Additional Summary Row -->
        <div class="row summary-card">
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h4>{{ $summary['total_qty'] }}</h4>
                        <p>Total Quantity</p>
                    </div>
                    <div class="icon"><i class="fas fa-cubes"></i></div>
                    <div class="small-box-footer">&nbsp;</div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h4>৳{{ number_format($summary['total_line_charge'], 2) }}</h4>
                        <p>Line Charge</p>
                    </div>
                    <div class="icon"><i class="fas fa-road"></i></div>
                    <div class="small-box-footer">&nbsp;</div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h4>৳{{ number_format($summary['total_scale_fee'], 2) }}</h4>
                        <p>Scale Fee</p>
                    </div>
                    <div class="icon"><i class="fas fa-weight"></i></div>
                    <div class="small-box-footer">&nbsp;</div>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6 col-12">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h4>৳{{ number_format($summary['total_other_cost'], 2) }}</h4>
                        <p>Other Cost</p>
                    </div>
                    <div class="icon"><i class="fas fa-receipt"></i></div>
                    <div class="small-box-footer">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Data Table -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-table"></i>&nbsp;Challan Posting Report
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if (session('success'))
                            <div class="alert alert-success m-2">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-warning m-2">{{ session('error') }}</div>
                        @endif

                        <table id="postedTable" class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Vendor</th>
                                    <th>Header ID</th>
                                    <th>Truck Number</th>
                                    <th>Challan No</th>
                                    <th>Ghat</th>
                                    <th>Destination</th>
                                    <th>Qty</th>
                                    <th>Carrying Bill</th>
                                    <th>Line Charge</th>
                                    <th>Scale Fee</th>
                                    <th>Other Cost</th>
                                    <th>Advance</th>
                                    <th>Adv. Fuel</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                    <td>{{ $item->vendor->name }}</td>
                                    <td class="text-center">{{ $item->headerid }}</td>
                                    <td class="text-center">{{ strtoupper($item->truck_number) }}</td>
                                    <td class="text-center">{{ $item->challan_no }}</td>
                                    <td class="text-center">{{ $item->ghat->name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->destination->name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->dest_qty }}</td>
                                    <td class="text-right">{{ number_format($item->carrying_bill, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->line_charge, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->scale_fee, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->other_cost, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->advance, 2) }}</td>
                                    <td class="text-center">{{ $item->advancePayment->fuelqty ?? '-' }}</td>
                                    <td class="text-center table-actions">
                                      <a href="{{ route('admin.programDetailsEdit', $item->id) }}" class="btn btn-info btn-xs">
                                          <i class="fas fa-edit"></i>
                                      </a>
                                      
                                      @if(in_array('30', $permissions) || in_array(30, $permissions))
                                          <form action="{{ route('programDetails.delete', $item->id) }}" method="POST" style="display: inline;">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')">
                                                  <i class="fas fa-trash"></i>
                                              </button>
                                          </form>
                                      @endif
                                  </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="8" class="text-right">TOTAL</td>
                                    <td class="text-center">{{ $summary['total_qty'] }}</td>
                                    <td class="text-right">{{ number_format($summary['total_carrying_bill'], 2) }}</td>
                                    <td class="text-right">{{ number_format($summary['total_line_charge'], 2) }}</td>
                                    <td class="text-right">{{ number_format($summary['total_scale_fee'], 2) }}</td>
                                    <td class="text-right">{{ number_format($summary['total_other_cost'], 2) }}</td>
                                    <td class="text-right">{{ number_format($summary['total_advance'], 2) }}</td>
                                    <td class="text-center">{{ $summary['total_fuel_qty'] }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Missing Header IDs Table -->
@if (isset($missingHeaderIds) && $missingHeaderIds->count() > 0)
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-exclamation-triangle"></i>&nbsp;Without Header IDs
                            <span class="badge badge-light ml-2">{{ $summary['missing_count'] }}</span>
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table id="missingTable" class="table table-bordered table-striped table-sm">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Vendor</th>
                                    <th>Header ID</th>
                                    <th>Truck Number</th>
                                    <th>Challan No</th>
                                    <th>Destination</th>
                                    <th>Previous Qty</th>
                                    <th>Qty</th>
                                    <th>Carrying Bill</th>
                                    <th>Advance Cash</th>
                                    <th>Fuel Qty</th>
                                    <th>Fuel Token</th>
                                    <th>Fuel Amount</th>
                                    <th>Pump Name</th>
                                    <th>Line Charge</th>
                                    <th>Scale Fee</th>
                                    <th>Other Cost</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($missingHeaderIds as $key => $item)
                                <tr>
                                    <td class="text-center">{{ $key + 1 }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</td>
                                    <td>{{ $item->vendor->name }}</td>
                                    <td class="text-center">{{ $item->headerid ?? '-' }}</td>
                                    <td class="text-center">{{ strtoupper($item->truck_number) }}</td>
                                    <td class="text-center">{{ $item->challan_no }}</td>
                                    <td class="text-center">{{ $item->destination->name ?? '-' }}</td>
                                    <td class="text-center">{{ $item->old_qty ?? '-' }}</td>
                                    <td class="text-center">{{ $item->dest_qty }}</td>
                                    <td class="text-right">{{ number_format($item->carrying_bill, 2) }}</td>
                                    <td class="text-right">{{ $item->advancePayment->cashamount ?? '-' }}</td>
                                    <td class="text-center">{{ $item->advancePayment->fuelqty ?? '-' }}</td>
                                    <td class="text-center">{{ $item->advancePayment->fueltoken ?? '-' }}</td>
                                    <td class="text-right">{{ $item->advancePayment->fuelamount ?? '-' }}</td>
                                    <td class="text-center">{{ $item->advancePayment->petrolPump->name ?? '-' }}</td>
                                    <td class="text-right">{{ number_format($item->line_charge, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->scale_fee, 2) }}</td>
                                    <td class="text-right">{{ number_format($item->other_cost, 2) }}</td>
                                    <td class="text-center table-actions">
                                        <a href="{{ route('admin.programDetailsEdit', $item->id) }}" class="btn btn-info btn-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if(in_array('30', $permissions) || in_array(30, $permissions))
                                            <form action="{{ route('programDetails.delete', $item->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@endsection

@section('script')
<script>
 $(function () {
    // Defer DataTables initialization for faster initial render
    setTimeout(function () {
        const dtConfig = {
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            dom: 'Bfrtip',
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            pageLength: 25,
            order: [[1, 'desc']],
            footerCallback: function (row, data, start, end, display) {
                // Keep footer intact - don't let DataTables sum it
            }
        };

        $('#postedTable').DataTable(dtConfig).buttons().container()
            .appendTo('#postedTable_wrapper .col-md-6:eq(0)');

        $('#missingTable').DataTable(dtConfig).buttons().container()
            .appendTo('#missingTable_wrapper .col-md-6:eq(0)');
    }, 100);
});
</script>
@endsection