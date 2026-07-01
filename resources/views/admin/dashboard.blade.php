@extends('admin.layouts.admin')

@section('content')

<style>
    /* ===== Cash Position Cards ===== */
    .cash-card {
        border-left: 4px solid;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 8px;
    }
    .cash-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
    }
    .cash-card.border-hand-open  { border-left-color: #28a745; }
    .cash-card.border-field-open { border-left-color: #007bff; }
    .cash-card.border-hand-close { border-left-color: #17a2b8; }
    .cash-card.border-field-close{ border-left-color: #fd7e14; }

    .cash-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    /* ===== Quick Action Cards ===== */
    .action-card {
        text-decoration: none !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 10px;
        overflow: hidden;
    }
    .action-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
        text-decoration: none !important;
    }
    .action-card:hover .action-icon-inner {
        transform: scale(1.15);
    }
    .action-icon-inner {
        transition: transform 0.25s ease;
    }

    /* ===== Section Titles ===== */
    .section-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding-bottom: 0.6rem;
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 1rem;
    }

    /* ===== Small Box Refinements ===== */
    .small-box .icon > i {
        transition: transform 0.3s ease;
    }
    .small-box:hover .icon > i {
        transform: scale(1.15) rotate(-5deg);
    }

    /* ===== Table Refinements ===== */
    #example1 thead th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
    }
    #example1 tbody td {
        vertical-align: middle;
        font-size: 0.85rem;
    }

    /* ===== Total Cash Banner ===== */
    .total-cash-banner {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border-radius: 10px;
        color: white;
    }

    /* ===== Responsive Fix ===== */
    @media (max-width: 576px) {
        .cash-card .h5 { font-size: 1rem; }
        .action-card .font-weight-bold { font-size: 0.7rem; }
    }
</style>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-8">
                <h1 class="m-0 text-dark font-weight-bold">Dashboard</h1>
            </div>
            <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">

        @php
            $vendor = \App\Models\Vendor::count();
            $runningMotherVessel = \App\Models\MotherVassel::where('status', '1')->count();
            $completedMotherVessel = \App\Models\MotherVassel::where('status', '2')->count();

            $todayTrans = \App\Models\Transaction::where(function ($q) {
                $q->where('tran_type', 'Advance')
                  ->where('date', \Carbon\Carbon::today()->format('Y-m-d'));
            })->orWhere(function ($q) {
                $q->where('table_type', 'Expenses')
                  ->where('date', \Carbon\Carbon::today()->format('Y-m-d'));
            })->get();

            $todayTotal = $todayTrans->sum('amount');
            $todayCount = $todayTrans->count();

            $b = cash_balances();
            $handChange  = $b['cashInHandClosing'] - $b['cashInHandOpening'];
            $fieldChange = $b['cashInFieldClosing'] - $b['cashInFieldOpening'];
            $totalOpening = $b['cashInHandOpening'] + $b['cashInFieldOpening'];
            $totalClosing = $b['cashInHandClosing'] + $b['cashInFieldClosing'];
            $netChange = $totalClosing - $totalOpening;
        @endphp


        <!-- ============================================ -->
        <!-- ROW 1: KEY METRICS                           -->
        <!-- ============================================ -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $runningMotherVessel }}</h3>
                        <p>Running Mother Vessels</p>
                    </div>
                    <div class="icon"><i class="fas fa-ship"></i></div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $completedMotherVessel }}</h3>
                        <p>Completed Mother Vessels</p>
                    </div>
                    <div class="icon"><i class="fas fa-clipboard-check"></i></div>
                </div>
            </div>
            <div class="col-lg-2 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $vendor }}</h3>
                        <p>Total Vendors</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ number_format($todayTotal, 0) }}</h3>
                        <p>Today's Transactions ({{ $todayCount }})</p>
                    </div>
                    <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                </div>
            </div>
        </div>


        <!-- ============================================ -->
        <!-- ROW 2: CASH POSITION                         -->
        <!-- ============================================ -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="section-title">
                    <i class="fas fa-wallet mr-2 text-primary"></i>Cash Position
                    <span class="text-muted font-weight-normal" style="text-transform: none; letter-spacing: 0;">
                        — {{ \Carbon\Carbon::parse($b['date'])->format('d F, Y') }}
                    </span>
                </h5>
            </div>

            <!-- Cash In Hand Opening -->
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card cash-card border-hand-open shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="cash-icon-box bg-success text-white mr-3">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-muted text-uppercase" style="font-size:0.7rem; letter-spacing:0.5px;">
                                    Cash In Hand
                                </div>
                                <div class="text-xs text-muted">Opening</div>
                                <div class="h5 font-weight-bold text-dark mb-0 mt-1">
                                    {{ number_format($b['cashInHandOpening'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash In Field Opening -->
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card cash-card border-field-open shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="cash-icon-box bg-primary text-white mr-3">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-muted text-uppercase" style="font-size:0.7rem; letter-spacing:0.5px;">
                                    Cash In Field
                                </div>
                                <div class="text-xs text-muted">Opening</div>
                                <div class="h5 font-weight-bold text-dark mb-0 mt-1">
                                    {{ number_format($b['cashInFieldOpening'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash In Hand Closing -->
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card cash-card border-hand-close shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="cash-icon-box bg-info text-white mr-3">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-muted text-uppercase" style="font-size:0.7rem; letter-spacing:0.5px;">
                                    Cash In Hand
                                </div>
                                <div class="text-xs text-muted">
                                    Closing
                                    <span class="{{ $handChange >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.7rem;">
                                        {{ $handChange >= 0 ? '+' : '' }}{{ number_format($handChange, 2) }}
                                    </span>
                                </div>
                                <div class="h5 font-weight-bold text-dark mb-0 mt-1">
                                    {{ number_format($b['cashInHandClosing'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash In Field Closing -->
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <div class="card cash-card border-field-close shadow-sm">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="cash-icon-box bg-warning text-white mr-3">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="small text-muted text-uppercase" style="font-size:0.7rem; letter-spacing:0.5px;">
                                    Cash In Field
                                </div>
                                <div class="text-xs text-muted">
                                    Closing
                                    <span class="{{ $fieldChange >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.7rem;">
                                        {{ $fieldChange >= 0 ? '+' : '' }}{{ number_format($fieldChange, 2) }}
                                    </span>
                                </div>
                                <div class="h5 font-weight-bold text-dark mb-0 mt-1">
                                    {{ number_format($b['cashInFieldClosing'], 2) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Cash Summary Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="total-cash-banner p-3 d-flex flex-wrap align-items-center justify-content-between">
                    <div class="d-flex align-items-center mr-4 mb-2 mb-md-0">
                        <i class="fas fa-coins fa-2x mr-3 text-warning"></i>
                        <div>
                            <div class="small text-uppercase" style="letter-spacing:1px; color:#adb5bd;">Total Cash (Opening)</div>
                            <div class="h4 font-weight-bold mb-0">{{ number_format($totalOpening, 2) }}</div>
                        </div>
                    </div>
                    <div class="text-center px-3 mb-2 mb-md-0">
                        <i class="fas fa-arrow-right fa-2x" style="color:#adb5bd;"></i>
                        <div class="small mt-1 {{ $netChange >= 0 ? 'text-success' : 'text-danger' }}" style="font-size:0.75rem;">
                            Net {{ $netChange >= 0 ? '+' : '' }}{{ number_format($netChange, 2) }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="text-right">
                            <div class="small text-uppercase" style="letter-spacing:1px; color:#adb5bd;">Total Cash (Closing)</div>
                            <div class="h4 font-weight-bold mb-0">{{ number_format($totalClosing, 2) }}</div>
                        </div>
                        <i class="fas fa-vault fa-2x ml-3 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>


        <!-- ============================================ -->
        <!-- ROW 3: QUICK ACTIONS                         -->
        <!-- ============================================ -->
        <div class="row">
            <div class="col-12">
                <h5 class="section-title">
                    <i class="fas fa-bolt mr-2 text-warning"></i>Quick Actions
                </h5>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-lg-center mb-4" style="gap: 0.75rem;">

            <a href="{{ route('challanPostingVendorReport') }}" class="action-card card shadow-sm border-0 bg-gradient-primary text-white" style="width:175px; min-width:150px;">
                <div class="card-body text-center p-3">
                    <div class="action-icon-inner mb-2"><i class="fas fa-file-invoice fa-2x"></i></div>
                    <div class="font-weight-bold" style="font-size:0.8rem;">Challan Posting</div>
                    <div class="mt-1" style="font-size:0.65rem; opacity:0.8;">Check Report</div>
                </div>
            </a>

            <a href="{{ route('vendorLedger') }}" class="action-card card shadow-sm border-0 bg-gradient-success text-white" style="width:175px; min-width:150px;">
                <div class="card-body text-center p-3">
                    <div class="action-icon-inner mb-2"><i class="fas fa-book fa-2x"></i></div>
                    <div class="font-weight-bold" style="font-size:0.8rem;">Vendor Ledger</div>
                    <div class="mt-1" style="font-size:0.65rem; opacity:0.8;">Check Ledger</div>
                </div>
            </a>

            <a href="{{ route('admin.addProgram') }}" class="action-card card shadow-sm border-0 bg-gradient-info text-white" style="width:175px; min-width:150px;">
                <div class="card-body text-center p-3">
                    <div class="action-icon-inner mb-2"><i class="fas fa-clipboard-list fa-2x"></i></div>
                    <div class="font-weight-bold" style="font-size:0.8rem;">Before Challan</div>
                    <div class="mt-1" style="font-size:0.65rem; opacity:0.8;">Check Posting</div>
                </div>
            </a>

            <a href="{{ route('admin.afterPostProgram') }}" class="action-card card shadow-sm border-0 bg-gradient-warning text-white" style="width:175px; min-width:150px;">
                <div class="card-body text-center p-3">
                    <div class="action-icon-inner mb-2"><i class="fas fa-clipboard-check fa-2x"></i></div>
                    <div class="font-weight-bold" style="font-size:0.8rem;">After Challan</div>
                    <div class="mt-1" style="font-size:0.65rem; opacity:0.8;">Check Posting</div>
                </div>
            </a>

            <a href="{{ route('admin.allProgram') }}" class="action-card card shadow-sm border-0 bg-gradient-danger text-white" style="width:175px; min-width:150px;">
                <div class="card-body text-center p-3">
                    <div class="action-icon-inner mb-2"><i class="fas fa-th-list fa-2x"></i></div>
                    <div class="font-weight-bold" style="font-size:0.8rem;">All Programs</div>
                    <div class="mt-1" style="font-size:0.65rem; opacity:0.8;">View All</div>
                </div>
            </a>

        </div>


        <!-- ============================================ -->
        <!-- ROW 4: TODAY'S TRANSACTIONS (Permission)     -->
        <!-- ============================================ -->
        @if(in_array('1', json_decode(auth()->user()->role->permission)))
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header border-0" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="card-title text-white font-weight-bold mb-0">
                                <i class="fas fa-list-alt mr-2"></i>Today's Transactions
                                <span class="badge badge-light ml-2 font-weight-normal">{{ $todayCount }} Records</span>
                            </h3>
                            <span class="badge badge-pill badge-warning text-dark font-weight-bold" style="font-size:0.85rem;">
                                Total: {{ number_format($todayTotal, 2) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table id="example1" class="table table-bordered table-striped table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center" style="width:50px;">#</th>
                                    <th class="text-center" style="width:100px;">Vch No.</th>
                                    <th class="text-center" style="width:100px;">Date</th>
                                    <th>Description</th>
                                    <th class="text-center" style="width:90px;">Type</th>
                                    <th class="text-center" style="width:90px;">Payment</th>
                                    <th class="text-right" style="width:130px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($todayTrans as $key => $data)
                                <tr>
                                    <td class="text-center text-muted">{{ $key + 1 }}</td>
                                    <td class="text-center" style="font-family:monospace;">{{ $data->tran_id ?? '-' }}</td>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $data->tran_type === 'Advance' ? 'warning' : 'danger' }} mr-1">
                                            {{ $data->tran_type }}
                                        </span>
                                        <strong>{{ $data->chartOfAccount->account_name ?? '' }}</strong>
                                        @if($data->description)
                                            <br><small class="text-muted">{{ $data->description }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-secondary">{{ $data->table_type ?? '' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $data->payment_type === 'Cash' ? 'success' : 'info' }} px-2">
                                            {{ $data->payment_type }}
                                        </span>
                                    </td>
                                    <td class="text-right font-weight-bold" style="font-family:monospace;">
                                        {{ number_format($data->amount, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block" style="opacity:0.3;"></i>
                                        No transactions found for today.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if($todayCount > 0)
                            <tfoot>
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="6" class="text-right text-uppercase" style="font-size:0.8rem;">Grand Total</td>
                                    <td class="text-right text-danger" style="font-family:monospace; font-size:0.95rem;">
                                        {{ number_format($todayTotal, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif


    </div>
</section>

@endsection

@section('script')
<script>
 $(function () {
    $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 25,
        "order": [[0, "asc"]],
        "buttons": [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-default',
                text: '<i class="fas fa-copy"></i> Copy'
            },
            {
                extend: 'csv',
                className: 'btn btn-sm btn-default',
                text: '<i class="fas fa-file-csv"></i> CSV'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-default',
                text: '<i class="fas fa-file-excel"></i> Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-default',
                text: '<i class="fas fa-file-pdf"></i> PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-default',
                text: '<i class="fas fa-print"></i> Print'
            }
        ],
        "language": {
            "emptyTable": "No transactions found for today.",
            "search": "<div class='input-group input-group-sm'><div class='input-group-prepend'><span class='input-group-text bg-white border-right-0'><i class='fas fa-search'></i></span></div>",
            "searchPlaceholder": "Search transactions...",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "No entries available",
            "paginate": {
                "previous": "<i class='fas fa-chevron-left'></i>",
                "next": "<i class='fas fa-chevron-right'></i>"
            }
        },
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtipB'
    });
});
</script>
@endsection