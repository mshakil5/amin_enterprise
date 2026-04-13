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

                <!-- Toast Container -->
                <div class="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

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
                                            <th width="100">Date</th>
                                            <th width="100">Client</th>
                                            <th width="250">Bill List</th>
                                            <th width="110">Receive Type</th>
                                            <th class="text-right" width="80">Qty</th>
                                            <th class="text-right" width="110">Total Amount</th>
                                            <th class="text-right" width="100">Maintenance</th>
                                            <th class="text-right" width="100">Scale Charge</th>
                                            <th class="text-right" width="90">Other Exp</th>
                                            <th class="text-right" width="90">Other Rcv</th>
                                            <th class="text-right" width="110">Net Amount</th>
                                            <th width="130">Status</th>
                                            <th width="70">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($billReceive as $index => $bill)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ date('d-m-Y', strtotime($bill->date)) }}</td>
                                                <td class="text-center">{{ $bill->client->name ?? '' }}</td>
                                                
                                                <!-- FIXED BILL LIST COLUMN -->
                                                <td>
                                                    @if($bill->bill_list)
                                                        @php
                                                            // Split the comma-separated string into an array
                                                            $bills = explode(',', $bill->bill_list);
                                                            $displayLimit = 3; // Only show first 3 badges
                                                            $displayBills = array_slice($bills, 0, $displayLimit);
                                                            $remainingCount = count($bills) - $displayLimit;
                                                            $hiddenBills = implode(', ', array_slice($bills, $displayLimit));
                                                        @endphp
                                                        
                                                        <div class="d-flex flex-wrap" style="gap: 4px;">
                                                            @foreach($displayBills as $b)
                                                                <span class="badge badge-secondary text-sm">{{ trim($b) }}</span>
                                                            @endforeach
                                                            
                                                            @if($remainingCount > 0)
                                                                <span class="badge badge-primary text-sm" 
                                                                      data-toggle="tooltip" 
                                                                      data-html="true" 
                                                                      data-placement="top" 
                                                                      title="{{ htmlspecialchars($hiddenBills) }}">
                                                                    +{{ $remainingCount }} more
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <!-- END FIXED BILL LIST COLUMN -->

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
                                                    @if($bill->receive_status == 1)
                                                        <button type="button" 
                                                                class="btn btn-xs btn-success receive-toggle" 
                                                                data-id="{{ $bill->id }}" 
                                                                data-status="0"
                                                                title="Click to mark as Not Received">
                                                            <i class="fas fa-check-circle mr-1"></i>Received
                                                        </button>
                                                    @else
                                                        <button type="button" 
                                                                class="btn btn-xs btn-danger receive-toggle" 
                                                                data-id="{{ $bill->id }}" 
                                                                data-status="1"
                                                                title="Click to mark as Received">
                                                            <i class="fas fa-times-circle mr-1"></i>Not Received
                                                        </button>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        
                                                        <button type="button" class="btn btn-danger delete-btn" 
                                                                data-id="{{ $bill->id }}" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>

                                                        <a href="{{ route('admin.getReceivablesDetails', $bill->id) }}" class="btn btn-success"  title="Details">
                                                            Details
                                                        </a>

                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="font-weight-bold bg-light">
                                            <td colspan="5" class="text-right">Total:</td>
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
        var table = $('#billReceiveTable').DataTable({
            responsive: true,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 } // Disable sorting on Action column
            ],
            // Add fixed column widths so it doesn't collapse
            autoWidth: false 
        });

        // Initialize Tooltips (Important for the "+ X more" button to work)
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body', // Keeps tooltip inside the table bounds
            boundary: 'window'
        });

        // Delete button handler
        $('.delete-btn').on('click', function() {
            var id = $(this).data('id');
            var deleteUrl = '{{ route("admin.bill-receives.destroy", ":id") }}';
            deleteUrl = deleteUrl.replace(':id', id);
            $('#deleteForm').attr('action', deleteUrl);
            $('#deleteModal').modal('show');
        });

        // Receive Status Toggle
        $(document).on('click', '.receive-toggle', function() {
            var btn = $(this);
            var id = btn.data('id');
            var status = btn.data('status');
            var csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Updating...');

            $.ajax({
                url: '/admin/bill-receives/update-receive-status',
                method: 'POST',
                data: {
                    _token: csrfToken,
                    id: id,
                    receive_status: status
                },
                success: function(response) {
                    if (response.status == 1) {
                        btn.removeClass('btn-danger').addClass('btn-success');
                        btn.data('status', '0');
                        btn.html('<i class="fas fa-check-circle mr-1"></i>Received');
                        btn.attr('title', 'Click to mark as Not Received');
                        showToast('Marked as Received', 'success');
                    } else {
                        btn.removeClass('btn-success').addClass('btn-danger');
                        btn.data('status', '1');
                        btn.html('<i class="fas fa-times-circle mr-1"></i>Not Received');
                        btn.attr('title', 'Click to mark as Received');
                        showToast('Marked as Not Received', 'warning');
                    }
                },
                error: function(xhr) {
                    var msg = 'Failed to update status.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    showToast(msg, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false);
                }
            });
        });

        // Toast notification function
        function showToast(message, type) {
            var bgColor = type === 'success' ? 'bg-success' : 
                        type === 'warning' ? 'bg-warning' : 'bg-danger';
            var textColor = type === 'warning' ? 'text-dark' : 'text-white';

            var toast = $(`
                <div id="dynamicToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" 
                    data-delay="3000" style="min-width: 300px;">
                    <div class="toast-body ${bgColor} ${textColor} d-flex align-items-center px-3 py-2">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'warning' ? 'fa-exclamation-circle' : 'fa-times-circle'} mr-2"></i>
                        ${message}
                        <button type="button" class="ml-auto ${textColor}" data-dismiss="toast" style="background:none;border:none;font-size:1.2rem;">
                            &times;
                        </button>
                    </div>
                </div>
            `);

            $('.toast-container').append(toast);
            toast.toast('show');

            toast.on('hidden.bs.toast', function() {
                toast.remove();
            });
        }

    });
</script>
@endsection