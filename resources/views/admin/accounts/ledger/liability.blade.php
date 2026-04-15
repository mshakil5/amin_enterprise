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
                            {{ $accountName }} - Ledger
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
                                <a href="{{ route('admin.accounts.liability', $id) }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </a>
                            </div>
                        </form>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="info-box bg-info">
                                    <span class="info-box-icon"><i class="fas fa-arrow-down"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Debit</span>
                                        <span class="info-box-number">{{ number_format($totalDrAmount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-arrow-up"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Credit</span>
                                        <span class="info-box-number">{{ number_format($totalCrAmount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box {{ $totalBalance >= 0 ? 'bg-warning' : 'bg-danger' }}">
                                    <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Closing Balance</span>
                                        <span class="info-box-number">
                                            {{ number_format(abs($totalBalance), 2) }} 
                                            {{ $totalBalance >= 0 ? 'Cr' : 'Dr' }}
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
                                        <th class="text-center" width="10%">Payment Type</th>
                                        <th class="text-center" width="10%">Ref</th>
                                        <th class="text-center" width="10%">Type</th>
                                        <th class="text-right" width="10%">Debit</th>
                                        <th class="text-right" width="10%">Credit</th>
                                        <th class="text-right" width="10%">Balance</th>
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
                                            $isCredit = in_array($transaction->tran_type, ['Received']);
                                            $isDebit = in_array($transaction->tran_type, ['Payment']);
                                            
                                            $debitAmount = $isDebit ? $transaction->at_amount : 0;
                                            $creditAmount = $isCredit ? $transaction->at_amount : 0;
                                            
                                            // Show current balance first
                                            $currentRowBalance = $runningBalance;
                                            
                                            // Then reverse the effect for next row (going backwards)
                                            // Debit decreased balance, so going back: add it
                                            // Credit increased balance, so going back: subtract it
                                            $runningBalance = $runningBalance + $debitAmount - $creditAmount;
                                        @endphp
                                        
                                        <tr>
                                            <td class="text-center">{{ $sl++ }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($transaction->date)->format('d-m-Y') }}
                                            </td>
                                            <td>
                                                {{ $transaction->description ?? 'N/A' }}

                                            </td>
                                            <td class="text-center">{{ $transaction->payment_type ?? 'N/A' }}</td>
                                            <td class="text-center">
                                                {{ $transaction->ref ?? 'N/A' }} <br> {{ $transaction->tran_id ?? 'N/A' }}

                                            </td>
                                            <td class="text-center">
                                                @if($isDebit)
                                                    <span class="badge badge-info">{{ $transaction->tran_type }}</span>
                                                @elseif($isCredit)
                                                    <span class="badge badge-success">{{ $transaction->tran_type }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ $transaction->tran_type }}</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                {{ $isDebit ? number_format($transaction->at_amount, 2) : '' }}
                                            </td>
                                            <td class="text-right">
                                                {{ $isCredit ? number_format($transaction->at_amount, 2) : '' }}
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                {{ number_format(abs($currentRowBalance), 2) }} 
                                                <small>{{ $currentRowBalance >= 0 ? 'Cr' : 'Dr' }}</small>
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
                                                No transactions found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($data->isNotEmpty())
                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="6" class="text-right">Total:</td>
                                        <td class="text-right text-success">{{ number_format($totalCrAmount, 2) }}</td>
                                        <td class="text-right text-info">{{ number_format($totalDrAmount, 2) }}</td>
                                        <td class="text-right">
                                            {{ number_format(abs($totalBalance), 2) }} 
                                            <small>{{ $totalBalance >= 0 ? 'Cr' : 'Dr' }}</small>
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
                searchPlaceholder: "Search transactions..."
            }
        });
    });
</script>

@endsection