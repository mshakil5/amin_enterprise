@extends('admin.layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<style>
    :root {
        --primary-color: #2c3e50;
        --success-color: #27ae60;
        --danger-color: #e74c3c;
        --warning-color: #f39c12;
        --info-color: #3498db;
        --dark-color: #1a252f;
        --light-bg: #f8f9fa;
    }

    .pl-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .pl-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--dark-color) 100%);
        padding: 25px 30px;
        color: #fff;
    }

    .pl-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 1.4rem;
    }

    .filter-section {
        background: var(--light-bg);
        padding: 20px 25px;
        border-bottom: 1px solid #e9ecef;
    }

    .filter-section label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }

    .btn-generate {
        background: linear-gradient(135deg, var(--success-color) 0%, #2ecc71 100%);
        border: none;
        padding: 10px 30px;
        border-radius: 8px;
        color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-generate:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
        color: #fff;
    }

    /* Summary Cards */
    .summary-card {
        border: none;
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .summary-card.revenue { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: #fff; }
    .summary-card.gross-profit { background: linear-gradient(135deg, #2980b9 0%, #3498db 100%); color: #fff; }
    .summary-card.net-profit { background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%); color: #fff; }
    .summary-card.expenses { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: #fff; }

    .summary-card .icon {
        font-size: 2.5rem;
        opacity: 0.3;
        position: absolute;
        right: 15px;
        top: 15px;
    }

    .summary-card .value {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 10px 0 5px;
    }

    .summary-card .label {
        font-size: 0.8rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .summary-card .change {
        font-size: 0.75rem;
        margin-top: 8px;
    }

    /* P&L Table */
    .pl-table {
        width: 100%;
        border-collapse: collapse;
    }

    .pl-table thead th {
        background: var(--primary-color);
        color: #fff;
        padding: 12px 15px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pl-table .section-header {
        background: var(--primary-color);
        color: #fff;
        padding: 12px 15px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .pl-table .subsection-header {
        background: #ecf0f1;
        padding: 10px 15px;
        font-weight: 600;
        color: var(--primary-color);
        font-size: 0.85rem;
    }

    .pl-table .account-row td {
        padding: 10px 15px 10px 35px;
        border-bottom: 1px solid #f1f1f1;
    }

    .pl-table .account-row:hover {
        background: #f8f9fa;
    }

    .pl-table .subtotal-row td {
        background: #f8f9fa;
        padding: 10px 15px;
        font-weight: 600;
        border-top: 2px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
    }

    .pl-table .total-row td {
        background: var(--primary-color);
        color: #fff;
        padding: 12px 15px;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .pl-table .net-profit-row td {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
        color: #fff;
        padding: 15px;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .pl-table .net-profit-row.negative td {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
    }

    .pl-table .amount {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: 500;
    }

    .pl-table .amount.negative {
        color: var(--danger-color);
    }

    .pl-table .amount.positive {
        color: var(--success-color);
    }

    /* Progress Bars */
    .progress-thin {
        height: 6px;
        border-radius: 3px;
        background: #e9ecef;
    }

    .progress-thin .progress-bar {
        border-radius: 3px;
    }

    /* Print Styles */
    @media print {
        .no-print, .main-sidebar, .main-footer, .breadcrumb {
            display: none !important;
        }
        .content-wrapper {
            margin: 0 !important;
            padding: 10px !important;
        }
        .pl-card {
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }
        .pl-header {
            background: #333 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .pl-table .section-header,
        .pl-table .total-row,
        .pl-table .net-profit-row {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .summary-card {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .pl-table .account-row td {
            padding: 8px 15px 8px 35px;
            font-size: 11px;
        }
    }

    /* Flatpickr */
    .flatpickr-calendar {
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }

    @media (max-width: 768px) {
        .summary-card .value {
            font-size: 1.2rem;
        }
    }
</style>

<section class="content pt-3">
    <div class="container-fluid">

        {{-- Main Card --}}
        <div class="card pl-card">
            
            {{-- Header --}}
            <div class="pl-header d-flex justify-content-between align-items-center no-print">
                <h3><i class="fas fa-chart-pie mr-2"></i>Profit & Loss Statement</h3>
                <div class="d-flex gap-2">
                    <button onclick="window.print();" class="btn btn-light">
                        <i class="fas fa-print mr-1"></i>Print
                    </button>
                </div>
            </div>

            {{-- Filter Section --}}
            <div class="filter-section no-print">
                <form action="{{ route('admin.profit-loss.generate') }}" method="POST" id="filterForm">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="text" name="start_date" id="start_date"
                                   class="form-control" value="{{ $startDate ?? now()->startOfMonth()->format('Y-m-d') }}"
                                   placeholder="Start Date" required>
                        </div>
                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="text" name="end_date" id="end_date"
                                   class="form-control" value="{{ $endDate ?? now()->format('Y-m-d') }}"
                                   placeholder="End Date" required>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-generate w-100">
                                <i class="fas fa-calculator mr-2"></i>Generate P&L
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-outline-secondary w-100" id="btnThisMonth">
                                <i class="fas fa-calendar mr-1"></i>This Month
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary quick-date" data-days="7">Last 7 Days</button>
                                <button type="button" class="btn btn-outline-primary quick-date" data-days="30">Last 30 Days</button>
                                <button type="button" class="btn btn-outline-primary quick-date" data-days="90">Last 90 Days</button>
                                <button type="button" class="btn btn-outline-primary quick-date" data-days="365">This Year</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Content --}}
            <div class="card-body p-4">
                
                @if(isset($netProfit))

                {{-- Company Header for Print --}}
                <div class="text-center mb-4">
                    <h2 class="font-weight-bold">M/S AMIN ENTERPRISE</h2>
                    <h5 class="text-muted">BSRM PROGRAM</h5>
                    <div class="badge badge-secondary p-2 mt-2" style="font-size: 0.9rem;">
                        PROFIT AND LOSS STATEMENT
                    </div>
                    <p class="mt-2 text-muted">
                        For the period from <strong>{{ \Carbon\Carbon::parse($startDate)->format('d F, Y') }}</strong> 
                        to <strong>{{ \Carbon\Carbon::parse($endDate)->format('d F, Y') }}</strong>
                        <span class="badge badge-info ml-2">{{ $periodDays ?? 0 }} Days</span>
                    </p>
                </div>

                {{-- Summary Cards --}}
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="summary-card revenue position-relative">
                            <i class="fas fa-arrow-down icon"></i>
                            <div class="label">Net Revenue</div>
                            <div class="value">{{ number_format($netRevenue, 2) }}</div>
                            <div class="change">
                                <i class="fas fa-chart-line mr-1"></i>Gross: {{ number_format($totalGrossIncome, 2) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="summary-card gross-profit position-relative">
                            <i class="fas fa-percentage icon"></i>
                            <div class="label">Gross Profit</div>
                            <div class="value">{{ number_format($grossProfit, 2) }}</div>
                            <div class="change">
                                <i class="fas fa-chart-bar mr-1"></i>Margin: {{ number_format($grossProfitMargin, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="summary-card expenses position-relative">
                            <i class="fas fa-arrow-up icon"></i>
                            <div class="label">Total Expenses</div>
                            <div class="value">{{ number_format($totalOperatingExpenses + $totalCogs + $totalDepreciation, 2) }}</div>
                            <div class="change">
                                <i class="fas fa-chart-pie mr-1"></i>Opex Ratio: {{ number_format($operatingExpenseRatio, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="summary-card net-profit position-relative {{ $netProfit < 0 ? 'negative' : '' }}">
                            <i class="fas fa-coins icon"></i>
                            <div class="label">Net Profit</div>
                            <div class="value">{{ number_format($netProfit, 2) }}</div>
                            <div class="change">
                                @if($profitChange >= 0)
                                    <span class="text-light"><i class="fas fa-arrow-up mr-1"></i>{{ number_format($profitChange, 1) }}% vs prev</span>
                                @else
                                    <span class="text-light"><i class="fas fa-arrow-down mr-1"></i>{{ number_format(abs($profitChange), 1) }}% vs prev</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- P&L Main Table --}}
                <div class="table-responsive">
                    <table class="pl-table">
                        <thead>
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>Particulars</th>
                                <th class="amount" style="width: 150px;">Amount (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>

                            {{-- ==================== REVENUE ==================== --}}
                            <tr class="section-header">
                                <td colspan="3">
                                    <i class="fas fa-arrow-down mr-2"></i>REVENUE
                                </td>
                            </tr>

                            @foreach($incomeByAccount as $index => $income)
                                <tr class="account-row">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $income->chartOfAccount->account_name ?? 'Other Income' }}</td>
                                    <td class="amount positive">{{ number_format($income->total, 2) }}</td>
                                </tr>
                            @endforeach

                            @if($totalRefunds > 0)
                                <tr class="account-row">
                                    <td></td>
                                    <td class="text-danger">Less: Income Refunds</td>
                                    <td class="amount negative">({{ number_format($totalRefunds, 2) }})</td>
                                </tr>
                            @endif

                            <tr class="subtotal-row">
                                <td></td>
                                <td><strong>Net Revenue</strong></td>
                                <td class="amount"><strong>{{ number_format($netRevenue, 2) }}</strong></td>
                            </tr>

                            {{-- ==================== COGS ==================== --}}
                            <tr class="section-header" style="margin-top: 10px;">
                                <td colspan="3">
                                    <i class="fas fa-box mr-2"></i>COST OF GOODS SOLD (COGS)
                                </td>
                            </tr>

                            @if($cogsByAccount->count() > 0)
                                @foreach($cogsByAccount as $index => $cogs)
                                    <tr class="account-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $cogs->chartOfAccount->account_name ?? 'COGS' }}</td>
                                        <td class="amount negative">({{ number_format($cogs->total, 2) }})</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="account-row">
                                    <td></td>
                                    <td class="text-muted">No COGS entries</td>
                                    <td class="amount">-</td>
                                </tr>
                            @endif

                            <tr class="subtotal-row">
                                <td></td>
                                <td><strong>Total Cost of Goods Sold</strong></td>
                                <td class="amount negative"><strong>({{ number_format($totalCogs, 2) }})</strong></td>
                            </tr>

                            {{-- ==================== GROSS PROFIT ==================== --}}
                            <tr class="total-row">
                                <td></td>
                                <td><i class="fas fa-equals mr-2"></i>GROSS PROFIT</td>
                                <td class="amount">{{ number_format($grossProfit, 2) }}</td>
                            </tr>

                            {{-- Gross Profit Margin Bar --}}
                            <tr>
                                <td colspan="3" style="padding: 8px 15px;">
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted mr-2" style="min-width: 80px;">G.P. Margin:</small>
                                        <div class="progress progress-thin flex-grow-1">
                                            <div class="progress-bar bg-info" style="width: {{ min($grossProfitMargin, 100) }}%">
                                                {{ number_format($grossProfitMargin, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- ==================== OPERATING EXPENSES ==================== --}}
                            <tr class="section-header">
                                <td colspan="3">
                                    <i class="fas fa-receipt mr-2"></i>OPERATING EXPENSES
                                </td>
                            </tr>

                            @if($expensesByAccount->count() > 0)
                                @foreach($expensesByAccount as $index => $expense)
                                    <tr class="account-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $expense->chartOfAccount->account_name ?? 'Expense' }}</td>
                                        <td class="amount negative">({{ number_format($expense->total, 2) }})</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr class="account-row">
                                    <td></td>
                                    <td class="text-muted">No Operating Expenses</td>
                                    <td class="amount">-</td>
                                </tr>
                            @endif

                            <tr class="subtotal-row">
                                <td></td>
                                <td><strong>Total Operating Expenses</strong></td>
                                <td class="amount negative"><strong>({{ number_format($totalOperatingExpenses, 2) }})</strong></td>
                            </tr>

                            {{-- ==================== NET OPERATING PROFIT ==================== --}}
                            <tr class="total-row">
                                <td></td>
                                <td><i class="fas fa-equals mr-2"></i>NET OPERATING PROFIT</td>
                                <td class="amount">{{ number_format($netOperatingProfit, 2) }}</td>
                            </tr>

                            {{-- ==================== OTHER INCOME/EXPENSES ==================== --}}
                            <tr class="section-header">
                                <td colspan="3">
                                    <i class="fas fa-exchange-alt mr-2"></i>OTHER INCOME / EXPENSES
                                </td>
                            </tr>

                            {{-- Depreciation --}}
                            @if($depreciationByAccount->count() > 0)
                                <tr class="subsection-header">
                                    <td colspan="3">Depreciation</td>
                                </tr>
                                @foreach($depreciationByAccount as $index => $dep)
                                    <tr class="account-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dep->chartOfAccount->account_name ?? 'Depreciation' }}</td>
                                        <td class="amount negative">({{ number_format($dep->total, 2) }})</td>
                                    </tr>
                                @endforeach
                            @endif

                            {{-- Asset Sales --}}
                            @if($assetSalesByAccount->count() > 0)
                                <tr class="subsection-header">
                                    <td colspan="3">Asset Sales / Disposal</td>
                                </tr>
                                @foreach($assetSalesByAccount as $index => $sale)
                                    <tr class="account-row">
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $sale->chartOfAccount->account_name ?? 'Asset Sale' }}</td>
                                        <td class="amount positive">{{ number_format($sale->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif

                            @if($depreciationByAccount->count() == 0 && $assetSalesByAccount->count() == 0)
                                <tr class="account-row">
                                    <td></td>
                                    <td class="text-muted">No Other Income/Expenses</td>
                                    <td class="amount">-</td>
                                </tr>
                            @endif

                            <tr class="subtotal-row">
                                <td></td>
                                <td><strong>Net Other Income/(Expenses)</strong></td>
                                <td class="amount {{ $netOtherIncome >= 0 ? 'positive' : 'negative' }}">
                                    <strong>{{ $netOtherIncome >= 0 ? '' : '(' }}{{ number_format(abs($netOtherIncome), 2) }}{{ $netOtherIncome < 0 ? ')' : '' }}</strong>
                                </td>
                            </tr>

                            {{-- ==================== NET PROFIT ==================== --}}
                            <tr class="net-profit-row {{ $netProfit < 0 ? 'negative' : '' }}">
                                <td></td>
                                <td>
                                    <i class="fas fa-trophy mr-2"></i>
                                    {{ $netProfit >= 0 ? 'NET PROFIT' : 'NET LOSS' }}
                                </td>
                                <td class="amount">
                                    {{ $netProfit < 0 ? '(' : '' }}{{ number_format(abs($netProfit), 2) }}{{ $netProfit < 0 ? ')' : '' }}
                                </td>
                            </tr>

                            {{-- Net Profit Margin Bar --}}
                            <tr>
                                <td colspan="3" style="padding: 12px 15px;">
                                    <div class="d-flex align-items-center">
                                        <small class="text-muted mr-2" style="min-width: 80px;">N.P. Margin:</small>
                                        <div class="progress progress-thin flex-grow-1">
                                            <div class="progress-bar {{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }}" style="width: {{ min(abs($netProfitMargin), 100) }}%">
                                                {{ number_format($netProfitMargin, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                {{-- Footer Signatures --}}
                <div class="row mt-5 pt-4 border-top">
                    <div class="col-3 text-center">
                        <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                        <p class="mt-2 font-weight-bold small">Prepared By</p>
                    </div>
                    <div class="col-3 text-center">
                        <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                        <p class="mt-2 font-weight-bold small">Checked By</p>
                    </div>
                    <div class="col-3 text-center">
                        <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                        <p class="mt-2 font-weight-bold small">Approved By</p>
                    </div>
                    <div class="col-3 text-center">
                        <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                        <p class="mt-2 font-weight-bold small">Managing Director</p>
                    </div>
                </div>

                @else

                {{-- Empty State --}}
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Select Date Range</h4>
                    <p class="text-muted">Choose start and end dates to generate Profit & Loss Statement</p>
                    <p class="small text-muted mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Data available from <strong>20 July 2025</strong>
                    </p>
                </div>

                @endif
            </div>
        </div>

    </div>
</section>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
 $(document).ready(function() {
    // Initialize Flatpickr
    flatpickr("#start_date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        minDate: "2025-07-20",
        maxDate: "today",
        allowInput: false
    });

    flatpickr("#end_date", {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "d F Y",
        minDate: "2025-07-20",
        maxDate: "today",
        allowInput: false
    });

    // Link start and end dates
    $("#start_date").on("change", function(selectedDates) {
        if (selectedDates) {
            $("#end_date").set("minDate", selectedDates[0]);
        }
    });

    // This Month Button
    $("#btnThisMonth").on("click", function() {
        var start = moment().startOf('month').format('YYYY-MM-DD');
        var end = moment().format('YYYY-MM-DD');
        $("#start_date").set('selectedDates', [new Date(start)]);
        $("#end_date").set('selectedDates', [new Date(end)]);
        $("#filterForm").submit();
    });

    // Quick Date Buttons
    $(".quick-date").on("click", function() {
        var days = parseInt($(this).data('days'));
        var end = moment();
        var start = moment().subtract(days - 1, 'days');
        
        // Ensure start is not before 2025-07-20
        if (start.isBefore(moment('2025-07-20'))) {
            start = moment('2025-07-20');
        }

        $("#start_date").set('selectedDates', [start.toDate()]);
        $("#end_date").set('selectedDates', [end.toDate()]);
        $("#filterForm").submit();
    });
});
</script>
@endsection