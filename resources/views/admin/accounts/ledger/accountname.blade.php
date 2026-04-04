@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                
                <!-- Page Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0"><i class="fas fa-sitemap mr-2 text-primary"></i>Chart of Accounts</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>

                @php
                    // Calculate counts for summary cards
                    $assetsCount = $chartOfAccounts->where('account_head', 'Assets')->count();
                    $liabilitiesCount = $chartOfAccounts->where('account_head', 'Liabilities')->count();
                    $equityCount = $chartOfAccounts->where('account_head', 'Equity')->count();
                    $incomeCount = $chartOfAccounts->where('account_head', 'Income')->count();
                    $expensesCount = $chartOfAccounts->where('account_head', 'Expenses')->count();

                    // Route mapping to clean up the table loop
                    $headRoutes = [
                        'Assets' => '/admin/ledger/asset-details/',
                        'Expenses' => '/admin/ledger/expense-details/',
                        'Income' => '/admin/ledger/income-details/',
                        'Liabilities' => '/admin/ledger/liability-details/',
                        'Equity' => '/admin/ledger/equity-details/',
                    ];

                    // Badge color mapping
                    $headColors = [
                        'Assets' => 'info',
                        'Liabilities' => 'danger',
                        'Equity' => 'secondary',
                        'Income' => 'success',
                        'Expenses' => 'warning',
                    ];
                @endphp

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="info-box bg-info">
                            <span class="info-box-icon bg-info"><i class="fas fa-building"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Assets</span>
                                <span class="info-box-number">{{ $assetsCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="info-box bg-danger">
                            <span class="info-box-icon bg-danger"><i class="fas fa-money-check-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Liabilities</span>
                                <span class="info-box-number">{{ $liabilitiesCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="info-box bg-secondary">
                            <span class="info-box-icon bg-secondary"><i class="fas fa-hand-holding-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Equity</span>
                                <span class="info-box-number">{{ $equityCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon bg-success"><i class="fas fa-arrow-circle-down"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Income</span>
                                <span class="info-box-number">{{ $incomeCount }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon bg-warning"><i class="fas fa-arrow-circle-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Expenses</span>
                                <span class="info-box-number">{{ $expensesCount }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Table Card -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Active Account List</h3>
                        <div class="card-tools">
                            <span class="badge badge-primary">Total: {{ $chartOfAccounts->count() }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="table-responsive">
                            <table id="chartOfAccountsTable" class="table table-striped table-bordered table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" width="5%">SL</th>
                                        <th width="40%">Account Name</th>
                                        <th class="text-center" width="25%">Account Head</th>
                                        <th class="text-center" width="30%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($chartOfAccounts as $key => $account)
                                        @php
                                            $color = $headColors[$account->account_head] ?? 'secondary';
                                            $url = $headRoutes[$account->account_head] ?? '';
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $key + 1 }}</td>
                                            <td class="font-weight-bold">
                                                {{ $account->account_name }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-{{ $color }} badge-pill px-3 py-2">
                                                    {{ $account->account_head }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($url)
                                                    <a href="{{ url($url . $account->id) }}" 
                                                       class="btn btn-primary btn-sm px-3"
                                                       title="View Ledger">
                                                        <i class="fas fa-book mr-1"></i> View Ledger
                                                    </a>
                                                @else
                                                    <span class="text-muted small">N/A</span>
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
    </div>
</section>

@endsection

@section('script')
<script>
 $(document).ready(function () {
    $('#chartOfAccountsTable').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        pageLength: 25,
        order: [[1, 'asc']], // Sort by Account Name by default
        dom: '<"row mb-3"<"col-sm-6"B><"col-sm-6"f>>rtip',
        buttons: [
            {
                extend: 'copy',
                className: 'btn btn-sm btn-secondary',
                text: '<i class="fas fa-copy"></i> Copy'
            },
            {
                extend: 'csv',
                className: 'btn btn-sm btn-success',
                text: '<i class="fas fa-file-csv"></i> CSV'
            },
            {
                extend: 'excel',
                className: 'btn btn-sm btn-primary',
                text: '<i class="fas fa-file-excel"></i> Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger',
                text: '<i class="fas fa-file-pdf"></i> PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-dark',
                text: '<i class="fas fa-print"></i> Print'
            }
        ],
        language: {
            search: "",
            searchPlaceholder: "Search accounts..."
        }
    });
});
</script>
@endsection