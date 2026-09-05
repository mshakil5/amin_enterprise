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

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-gas-pump"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Fuel (Debit)</span>
                                <span class="info-box-number">{{ number_format($totalDebit, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-money-check-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Payment (Credit)</span>
                                <span class="info-box-number">{{ number_format($totalCredit, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-box {{ $balance >= 0 ? 'bg-warning' : 'bg-danger' }}">
                            <span class="info-box-icon"><i class="fas fa-balance-scale"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Closing Balance</span>
                                <span class="info-box-number">
                                    {{ number_format(abs($balance), 2) }} 
                                    {{ $balance >= 0 ? 'Dr' : 'Cr' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Card -->
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-book mr-1"></i> 
                            Ledger: {{ $pump->name }}
                        </h3>
                    </div>

                    <div class="card-body">
                        <!-- Date Filter Form -->
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
                                <a href="{{ route('admin.pump.ledger', $pump->id) }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-undo mr-1"></i> Reset
                                </a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table id="ledgerTable" class="table table-striped table-bordered table-sm">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center" width="5%">#</th>
                                        <th class="text-center" width="10%">Date</th>
                                        <th width="30%">Description</th>
                                        <th class="text-center" width="10%">Qty</th>
                                        <th class="text-right" width="15%">Debit</th>
                                        <th class="text-right" width="15%">Credit</th>
                                        <th class="text-right" width="15%">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        // Start with closing balance (since DESC order)
                                        $runningBalance = $balance; 
                                        $sl = 1; 
                                    @endphp
                                    
                                    @forelse($ledgerData as $row)
                                        @php
                                            // The balance to show for THIS row is the current running balance
                                            $currentRowBalance = $runningBalance;
                                            
                                            // Then reverse the effect for next row (going backwards in time)
                                            // Debit increases balance, so going back: subtract it
                                            // Credit decreases balance, so going back: add it
                                            $runningBalance = $runningBalance - $row['debit'] + $row['credit'];
                                        @endphp
                                        
                                        <tr>
                                            <td class="text-center">{{ $sl++ }}</td>
                                            <td class="text-center">{{ \Carbon\Carbon::parse($row['date'])->format('d-m-Y') }}</td>
                                            <td>
                                                {{ $row['description'] }}
                                                <br>
                                                <small class="text-muted">Ref: {{ $row['ref'] ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-center">{{ number_format($row['qty'], 2) }}</td>
                                            <td class="text-right text-info">
                                                {{ $row['debit'] > 0 ? number_format($row['debit'], 2) : '0.00' }}
                                            </td>
                                            <td class="text-right text-success">
                                                {{ $row['credit'] > 0 ? number_format($row['credit'], 2) : '0.00' }}
                                            </td>
                                            <td class="text-right font-weight-bold">
                                                {{ number_format(abs($currentRowBalance), 2) }} 
                                                <small>{{ $currentRowBalance >= 0 ? 'Dr' : 'Cr' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                No transactions found for this period.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($ledgerData->isNotEmpty())
                                <tfoot>
                                    <tr class="font-weight-bold bg-light">
                                        <td colspan="3" class="text-right">Total:</td>
                                        <td class="text-center">{{ number_format($totalQty, 2) }}</td>
                                        <td class="text-right text-info">{{ number_format($totalDebit, 2) }}</td>
                                        <td class="text-right text-success">{{ number_format($totalCredit, 2) }}</td>
                                        <td class="text-right">
                                            {{ number_format(abs($balance), 2) }} 
                                            <small>{{ $balance >= 0 ? 'Dr' : 'Cr' }}</small>
                                        </td>
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
                searchPlaceholder: "Search ledger..."
            }
        });
    });
</script>

@endsection