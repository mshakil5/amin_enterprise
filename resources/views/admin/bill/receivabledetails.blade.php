@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Receivable Details
            </h3>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        {{-- Summary Info Boxes --}}
        <div class="row mb-3">
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Date</span>
                        <span class="info-box-number" style="font-size:14px">{{ $billReceive->date }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-university"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Rcv Type</span>
                        <span class="info-box-number" style="font-size:14px">{{ $billReceive->rcv_type }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-weight"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Qty</span>
                        <span class="info-box-number">{{ $billReceive->qty }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Amount</span>
                        <span class="info-box-number">{{ number_format($billReceive->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Net Amount</span>
                        <span class="info-box-number">{{ number_format($billReceive->net_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bills Count</span>
                        <span class="info-box-number">{{ $programDetails->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Secondary summary row --}}
        <div class="card card-outline card-secondary mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Bill Summary</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row text-center">
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Maintenance</small>
                        <h6 class="mb-0">{{ number_format($billReceive->maintainance, 2) }}</h6>
                    </div>
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Scale Charge</small>
                        <h6 class="mb-0">{{ number_format($billReceive->scale_charge, 2) }}</h6>
                    </div>
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Other Exp</small>
                        <h6 class="mb-0">{{ number_format($billReceive->other_exp, 2) }}</h6>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Other Rcv</small>
                        <h6 class="mb-0">{{ number_format($billReceive->other_rcv, 2) }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- Program Details Grouped by Bill No --}}
        @foreach ($programDetails as $billNo => $details)
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-alt mr-1"></i>
                    Bill No: <strong>{{ $billNo }}</strong>
                </h3>
                <div class="card-tools">
                    <span class="badge badge-primary mr-2">{{ $details->count() }} records</span>
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="table-{{ $billNo }}" class="table table-striped table-bordered table-sm bill-table">
                        <thead class="thead-dark text-center">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Program ID</th>
                                <th>Consignment No</th>
                                <th>Truck Number</th>
                                <th>Challan No</th>
                                <th>After Date</th>
                                <th>Dest Qty</th>
                                <th>Old Qty</th>
                                <th>Carrying Bill</th>
                                <th>Old Carrying</th>
                                <th>Scale Fee</th>
                                <th>Line Charge</th>
                                <th>Transport Cost</th>
                                <th>Add. Cost</th>
                                <th>Other Cost</th>
                                <th>Advance</th>
                                <th>Due</th>
                                <th>Dest Status</th>
                                <th>Tran Status</th>
                                <th>Bill Status</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($details as $index => $detail)
                            <tr class="text-center">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->date }}</td>
                                <td>{{ $detail->programid }}</td>
                                <td>{{ $detail->consignmentno }}</td>
                                <td class="text-left">{{ $detail->truck_number }}</td>
                                <td>{{ $detail->challan_no }}</td>
                                <td>{{ $detail->after_date }}</td>
                                <td>{{ $detail->dest_qty }}</td>
                                <td>{{ $detail->old_qty }}</td>
                                <td>{{ number_format($detail->carrying_bill, 2) }}</td>
                                <td>{{ number_format($detail->old_carrying_bill, 2) }}</td>
                                <td>{{ number_format($detail->scale_fee, 2) }}</td>
                                <td>{{ number_format($detail->line_charge, 2) }}</td>
                                <td>{{ number_format($detail->transportcost, 2) }}</td>
                                <td>{{ number_format($detail->additional_cost, 2) }}</td>
                                <td>{{ number_format($detail->other_cost, 2) }}</td>
                                <td>{{ number_format($detail->advance, 2) }}</td>
                                <td class="{{ $detail->due < 0 ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' }}">
                                    {{ number_format($detail->due, 2) }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ $detail->dest_status ? 'success' : 'warning' }}">
                                        {{ $detail->dest_status ? 'Done' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $detail->tran_status ? 'success' : 'secondary' }}">
                                        {{ $detail->tran_status ? 'Done' : 'Pending' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $detail->bill_status ? 'success' : 'danger' }}">
                                        {{ $detail->bill_status ? 'Paid' : 'Unpaid' }}
                                    </span>
                                </td>
                                <td>{{ $detail->note ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-warning font-weight-bold text-center">
                            <tr>
                                <td colspan="7" class="text-right">Subtotal:</td>
                                <td>{{ $details->sum('dest_qty') }}</td>
                                <td>{{ $details->sum('old_qty') }}</td>
                                <td>{{ number_format($details->sum('carrying_bill'), 2) }}</td>
                                <td>{{ number_format($details->sum('old_carrying_bill'), 2) }}</td>
                                <td>{{ number_format($details->sum('scale_fee'), 2) }}</td>
                                <td>{{ number_format($details->sum('line_charge'), 2) }}</td>
                                <td>{{ number_format($details->sum('transportcost'), 2) }}</td>
                                <td>{{ number_format($details->sum('additional_cost'), 2) }}</td>
                                <td>{{ number_format($details->sum('other_cost'), 2) }}</td>
                                <td>{{ number_format($details->sum('advance'), 2) }}</td>
                                <td class="{{ $details->sum('due') < 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($details->sum('due'), 2) }}
                                </td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Grand Total Card --}}
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calculator mr-1"></i> Grand Total</h3>
            </div>
            <div class="card-body">
                @php $all = $programDetails->flatten(); @endphp
                <div class="row text-center">
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h5>{{ $all->sum('dest_qty') }}</h5>
                                <p>Total Dest Qty</p>
                            </div>
                            <div class="icon"><i class="fas fa-weight"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('carrying_bill'), 2) }}</h5>
                                <p>Total Carrying Bill</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('transportcost'), 2) }}</h5>
                                <p>Total Transport Cost</p>
                            </div>
                            <div class="icon"><i class="fas fa-truck"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('advance'), 2) }}</h5>
                                <p>Total Advance</p>
                            </div>
                            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('scale_fee'), 2) }}</h5>
                                <p>Total Scale Fee</p>
                            </div>
                            <div class="icon"><i class="fas fa-balance-scale"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box {{ $all->sum('due') < 0 ? 'bg-danger' : 'bg-success' }}">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('due'), 2) }}</h5>
                                <p>Total Due</p>
                            </div>
                            <div class="icon"><i class="fas fa-balance-scale-right"></i></div>
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

    // Collect all bill table IDs from Blade
    var billTables = @json($programDetails->keys());

    // Initialize DataTable for each bill group
    billTables.forEach(function(billNo) {
        var tableId = '#table-' + billNo;

        $(tableId).DataTable({
            responsive: true,
            paging: false,          // Show all rows (subtotal in tfoot)
            searching: true,
            ordering: true,
            info: false,
            autoWidth: false,
            dom: '<"row mb-2"<"col-sm-6"B><"col-sm-6"f>>rt',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="fas fa-copy"></i> Copy',
                    title: 'Bill No: ' + billNo
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="fas fa-file-csv"></i> CSV',
                    title: 'Bill No: ' + billNo
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-primary',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    title: 'Bill No: ' + billNo
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-danger',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    title: 'Bill No: ' + billNo,
                    orientation: 'landscape',
                    pageSize: 'A3'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-dark',
                    text: '<i class="fas fa-print"></i> Print',
                    title: 'Bill No: ' + billNo
                }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search bill " + billNo + "..."
            }
        });
    });

});
</script>
@endsection