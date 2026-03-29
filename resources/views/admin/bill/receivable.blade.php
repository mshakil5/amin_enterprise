@extends('admin.layouts.admin')

@section('content')

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Bill Receivables</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Bill Receivables</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-times-circle mr-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                @endif
                                
                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $billReceive->count() }}</h3>
                                <p>Total Bills</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($billReceive->sum('net_amount'), 2) }}</h3>
                                <p>Total Amount</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($billReceive->sum('qty'), 2) }}</h3>
                                <p>Total Quantity</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-weight-hanging"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ $billReceive->where('rcv_type', 'Bank')->count() }}</h3>
                                <p>Bank Receives</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-university"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-1"></i>
                            All Receivables
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($billReceive->isEmpty())
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i>
                                No receivables found.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table id="billReceiveTable" class="table table-bordered table-striped table-hover">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th width="50">SL</th>
                                            <th>Date</th>
                                            <th>Bill List</th>
                                            <th>Receive Type</th>
                                            <th class="text-right">Qty</th>
                                            <th class="text-right">Total Amount</th>
                                            <th class="text-right">Maintenance</th>
                                            <th class="text-right">Scale Charge</th>
                                            <th class="text-right">Other Exp</th>
                                            <th class="text-right">Other Rcv</th>
                                            <th class="text-right">Net Amount</th>
                                            <th>Status</th>
                                            <th width="120">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($billReceive as $index => $bill)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ date('d-m-Y', strtotime($bill->date)) }}</td>
                                                <td>
                                                    @if($bill->bill_list)
                                                        <span class="badge badge-secondary">
                                                            {{ str_replace(',', ', ', $bill->bill_list) }}
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($bill->rcv_type == 'Bank')
                                                        <span class="badge badge-primary">
                                                            <i class="fas fa-university mr-1"></i>Bank
                                                        </span>
                                                    @elseif($bill->rcv_type == 'Cash')
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-money-bill mr-1"></i>Cash
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">{{ $bill->rcv_type }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">{{ number_format($bill->qty, 2) }}</td>
                                                <td class="text-right">{{ number_format($bill->total_amount, 2) }}</td>
                                                <td class="text-right">{{ number_format($bill->maintainance, 2) }}</td>
                                                <td class="text-right">{{ number_format($bill->scale_charge, 2) }}</td>
                                                <td class="text-right">{{ number_format($bill->other_exp, 2) }}</td>
                                                <td class="text-right">{{ number_format($bill->other_rcv, 2) }}</td>
                                                <td class="text-right">
                                                    <strong>{{ number_format($bill->net_amount, 2) }}</strong>
                                                </td>
                                                <td>
                                                    @if($bill->status == 1)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        
                                                        <button type="button" class="btn btn-danger delete-btn" 
                                                                data-id="{{ $bill->id }}" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="4" class="text-right">Total:</td>
                                            <td class="text-right">{{ number_format($billReceive->sum('qty'), 2) }}</td>
                                            <td class="text-right">{{ number_format($billReceive->sum('total_amount'), 2) }}</td>
                                            <td class="text-right">{{ number_format($billReceive->sum('maintainance'), 2) }}</td>
                                            <td class="text-right">{{ number_format($billReceive->sum('scale_charge'), 2) }}</td>
                                            <td class="text-right">{{ number_format($billReceive->sum('other_exp'), 2) }}</td>
                                            <td class="text-right">{{ number_format($billReceive->sum('other_rcv'), 2) }}</td>
                                            <td class="text-right">{{ number_format($billReceive->sum('net_amount'), 2) }}</td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this bill receive?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>



@endsection

@section('script')
<script>
    $(function() {
        // Initialize DataTable
        $('#billReceiveTable').DataTable({
            responsive: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });

        // Delete button handler
         $('.delete-btn').on('click', function() {
            var id = $(this).data('id');
            var deleteUrl = '{{ route("admin.bill-receives.destroy", ":id") }}';
            deleteUrl = deleteUrl.replace(':id', id);
            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').modal('show');
        });
        
    });
</script>
@endsection