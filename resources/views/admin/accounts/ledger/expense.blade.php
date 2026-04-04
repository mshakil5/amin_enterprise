@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>

                <!-- Main Card -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book mr-1"></i> 
                            {{ $accountName }} - Expense Ledger
                        </h3>
                        <div class="card-tools">
                            <span class="badge badge-primary">
                                Account ID: {{ $id }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Alert Container -->
                        <div id="alert-container"></div>

                        <!-- Filter Form -->
                        <form method="GET" class="form-row mb-4 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-weight-bold">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ url()->current() }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </a>
                            </div>
                        </form>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Expense (Debit)</span>
                                        <span class="info-box-number">{{ number_format($totalDrAmount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box bg-warning">
                                    <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Closing Balance</span>
                                        <span class="info-box-number">
                                            {{ number_format(abs($totalBalance), 2) }} Dr
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transactions Table -->
                        <div class="table-responsive">
                            <table id="ledgerTable" class="table table-striped table-bordered table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" width="5%">#</th>
                                        <th class="text-center" width="10%">Date</th>
                                        <th width="25%">Description</th>
                                        <th class="text-center" width="12%">Payment Type</th>
                                        <th class="text-center" width="10%">Ref</th>
                                        <th class="text-center" width="10%">Type</th>
                                        <th class="text-right" width="12%">Debit</th>
                                        <th class="text-right" width="8%">Credit</th>
                                        <th class="text-right" width="12%">Balance</th>
                                        <th class="text-center" width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Start with closing balance (since DESC order)
                                        $runningBalance = $totalBalance;
                                        $sl = 1;
                                    @endphp

                                    @forelse($data as $transaction)
                                        @php
                                            // Show current balance first
                                            $currentRowBalance = $runningBalance;
                                            
                                            // Since this is purely an expense ledger (all debits), 
                                            // going backwards in time means we subtract the amount.
                                            $runningBalance = $runningBalance - $transaction->at_amount;
                                        @endphp
                                        
                                        <tr>
                                            <td class="text-center">{{ $sl++ }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}
                                            </td>
                                            <td>{{ $transaction->description ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $transaction->payment_type ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $transaction->ref ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-warning">{{ $transaction->tran_type }}</span>
                                            </td>
                                            <td class="text-right text-danger">
                                                {{ number_format($transaction->at_amount, 2) }}
                                            </td>
                                            <td class="text-right"></td>
                                            <td class="text-right font-weight-bold">
                                                {{ number_format($currentRowBalance, 2) }} 
                                                <small class="text-muted">Dr</small>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.expense.voucher', ['id' => $transaction->id]) }}" 
                                                   target="_blank" 
                                                   class="btn btn-info btn-xs" 
                                                   title="View Voucher">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                No expense transactions found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($data->isNotEmpty())
                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="6" class="text-right">Total:</td>
                                        <td class="text-right text-danger">{{ number_format($totalDrAmount, 2) }}</td>
                                        <td class="text-right">0.00</td>
                                        <td class="text-right">
                                            {{ number_format($totalBalance, 2) }} 
                                            <small class="text-muted">Dr</small>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                                @endif
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
    $(function () {
        $("#ledgerTable").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            searching: true,
            ordering: false,
            paging: false,
            info: false,
            dom: '<"row mb-3"<"col-sm-6"B><"col-sm-6"f>>rt',
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
                searchPlaceholder: "Search expenses..."
            }
        });
    });
</script>

@endsection