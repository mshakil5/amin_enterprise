@extends('admin.layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<style>
    /* Professional Styling */
    .cash-sheet-card { border-radius: 8px; border: none; }
    .table-header-debit { background-color: #f0f9ff !important; }
    .table-header-credit { background-color: #fff7ed !important; }
    
    #kt_datepicker { font-weight: 600; color: #333; cursor: pointer; }
    
    /* Print Specific Styles */
    @media print {
        .search-section, .action-section, .main-sidebar, .main-footer, .breadcrumb, .btn {
            display: none !important;
        }
        .content-wrapper { margin: 0 !important; padding: 0 !important; }
        .card { border: none !important; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
        .print-area { width: 100%; }
    }

    /* Flatpickr Customization */
    .flatpickr-calendar { box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; border: 1px solid #e2e8f0 !important; }
</style>

<section class="content pt-4">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center no-print">
            <div class="col-md-6">
                <form action="{{ route('admin.cashSheet.Search')}}" method="POST" class="d-flex align-items-center">
                    @csrf
                    <div class="input-group shadow-sm" style="max-width: 450px;">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </span>
                        </div>
                        <input type="text" name="searchDate" id="kt_datepicker"
                               class="form-control border-left-0 bg-white" 
                               value="{{ $date }}" 
                               placeholder="Select Date" readonly required>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-sync-alt mr-1"></i> Load Sheet
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="col-md-6 text-md-right mt-3 mt-md-0">
                <div class="btn-group shadow-sm">
                    <button onclick="window.print();" class="btn btn-outline-dark">
                        <i class="fas fa-print mr-1"></i> Print
                    </button>
                    <button id="exportExcel" class="btn btn-success">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm cash-sheet-card">
            <div class="card-body print-area">
                <div class="text-center mb-4">
                    <h1 class="font-weight-bold mb-1">M/S AMIN ENTERPRISE</h1>
                    <h5 class="text-muted">BSRM PROGRAM</h5>
                    <h4 class="mt-3 badge badge-secondary p-2">Cash Sheet: {{ \Carbon\Carbon::parse($date)->format('d F, Y') }}</h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="cashSheetTable">
                        <thead class="thead-light">
                            <tr>
                                <th rowspan="2" class="align-middle text-center" style="width: 100px;">Date</th>
                                <th rowspan="2" class="align-middle">Particulars</th>
                                <th rowspan="2" class="align-middle text-center">Vch No.</th>
                                <th rowspan="2" class="align-middle text-center">Chq No.</th>
                                <th colspan="2" class="text-center table-header-debit text-primary">DEBIT (Receipts)</th>
                                <th colspan="2" class="text-center table-header-credit text-danger">CREDIT (Payments)</th>
                            </tr>
                            <tr>
                                <th class="text-center table-header-debit" style="width: 120px;">Cash</th>
                                <th class="text-center table-header-debit" style="width: 120px;">Bank</th>
                                <th class="text-center table-header-credit" style="width: 120px;">Cash</th>
                                <th class="text-center table-header-credit" style="width: 120px;">Bank</th>
                            </tr>
                        </thead>

                        @php
                            $closingCashInOffice = $cashInHandOpening;
                            $closingCashInField = $cashInFieldOpening;
                            $closingBankInOffice = 0;
                            $debitIncomeInOfficeCash = 0;
                            $debitIncomeInFieldCash = 0;
                            $totalCashCredits = 0;
                            $totalBankCredits = 0;
                            $totalDebitTransferCash = 0;
                            $totalDebitTransferBank = 0;
                        @endphp

                        <tbody>
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
                                <td class="font-weight-bold">Cash In Hand (Opening Balance)</td>
                                <td></td>
                                <td></td>
                                <td class="text-right text-success font-weight-bold">{{ number_format($cashInHandOpening, 2) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
                                <td class="font-weight-bold">Cash In Field (Opening Balance)</td>
                                <td></td>
                                <td></td>
                                <td class="text-right text-success font-weight-bold">{{ number_format($cashInFieldOpening, 2) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
                                <td>Petty Cash (Entertainment)</td>
                                <td></td>
                                <td></td>
                                <td class="text-right font-weight-bold">{{ number_format($pettyCash, 2) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="table-warning">
                                <td class="text-center">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
                                <td>Suspense Account</td>
                                <td></td>
                                <td></td>
                                <td class="text-right font-weight-bold">{{ number_format($suspenseAccount, 2) }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>


                            <!-- receipt start -->
                            {{-- Logic Helper: Update Closing Balances --}}
                            @php
                                $updateBalances = function($item) use (&$closingCashInOffice, &$closingCashInField) {
                                    if ($item->account_id == 1) {
                                        $closingCashInOffice += $item->amount;
                                    } elseif ($item->account_id == 2) {
                                        $closingCashInField += $item->amount;
                                    }
                                };
                            @endphp

                            {{-- 1. Debit Transfers --}}
                            @foreach ($debitTransfer as $transfer)
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($transfer->date)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><span class="badge badge-light">Transfer</span> {{ $transfer->description ?? 'Inter-account Transfer' }}</span>
                                            <span class="badge px-2 py-1" style="background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;">
                                                {{ $transfer->account->type ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center small">{{ $transfer->tran_id ?? '' }}</td>
                                    <td></td>
                                    <td class="text-right font-weight-bold">
                                        {{ $transfer->payment_type === 'Cash' ? number_format($transfer->amount, 2) : '' }}
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ $transfer->payment_type === 'Bank' ? number_format($transfer->amount, 2) : '' }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                                
                                @php 
                                    $updateBalances($transfer); 
                                    // Add this logic here:
                                    if ($transfer->payment_type === 'Cash') {
                                        $totalDebitTransferCash += $transfer->amount;
                                    } elseif ($transfer->payment_type === 'Bank') {
                                        $totalDebitTransferBank += $transfer->amount;
                                    }
                                @endphp
                            @endforeach

                            {{-- 2. Liabilities (Cash & Bank) --}}
                            @foreach ([$liabilitiesInCash, $liabilitiesInBank] as $liabilityGroup)
                                @foreach ($liabilityGroup as $liability)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <strong>{{ $liability->chartOfAccount->account_name ?? 'Liability' }}</strong>
                                                    <div class="small text-muted">{{ $liability->description }}</div>
                                                </div>
                                                <span class="badge px-2 py-1" style="background-color: #fff3e0; color: #e65100; border: 1px solid #ffe0b2;">
                                                    {{ $liability->account->type ?? '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center small">{{ $liability->tran_id ?? '' }}</td>
                                        <td></td>
                                        <td class="text-right font-weight-bold">
                                            {{ $liability->payment_type === 'Cash' ? number_format($liability->amount, 2) : '' }}
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            {{ $liability->payment_type === 'Bank' ? number_format($liability->amount, 2) : '' }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                    @php $updateBalances($liability); @endphp
                                @endforeach
                            @endforeach

                            {{-- 3. Equity Received (Cash & Bank) --}}
                            @foreach ([$equityInCashReceived, $equityInBankReceived] as $equityGroup)
                                @foreach ($equityGroup as $equity)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($equity->date)->format('d-m-Y') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <span class="text-primary font-weight-bold">Equity:</span> {{ $equity->chartOfAccount->account_name ?? '' }}
                                                    <div class="small text-muted">{{ $equity->description }}</div>
                                                </div>
                                                <span class="badge px-2 py-1" style="background-color: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb;">
                                                    {{ $equity->account->type ?? '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center small">{{ $equity->tran_id ?? '' }}</td>
                                        <td></td>
                                        <td class="text-right font-weight-bold">
                                            {{ $equity->payment_type === 'Cash' ? number_format($equity->amount, 2) : '' }}
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            {{ $equity->payment_type === 'Bank' ? number_format($equity->amount, 2) : '' }}
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                    @php $updateBalances($equity); @endphp
                                @endforeach
                            @endforeach

                            {{-- 4. Incomes --}}
                            @foreach ($incomes as $income)
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($income->date)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="text-success font-weight-bold">Income:</span> {{ $income->chartOfAccount->account_name ?? '' }}
                                                @if ($income->tran_type === 'Wallet')
                                                    <span class="badge badge-info ml-1">{{ $income->vendor->name ?? '' }}</span>
                                                @endif
                                                <div class="small text-muted">{{ $income->description }}</div>
                                            </div>
                                            <span class="badge px-2 py-1" style="background-color: #f1f8e9; color: #33691e; border: 1px solid #dcedc8;">
                                                {{ $income->account->type ?? '' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center small">{{ $income->tran_id ?? '' }}</td>
                                    <td></td>
                                    <td class="text-right font-weight-bold">
                                        {{ $income->payment_type === 'Cash' ? number_format($income->amount, 2) : '' }}
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        {{ $income->payment_type === 'Bank' ? number_format($income->amount, 2) : '' }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                                @php
                                    $updateBalances($income);
                                    
                                    if ($income->payment_type === 'Cash') {
                                        $debitIncomeInOfficeCash += $income->amount;
                                    } else {
                                        $debitIncomeInFieldCash += $income->amount;
                                    }
                                @endphp
                            @endforeach
                            <!-- receipt end -->

                            

                            {{-- @php
                                $totalCashDebit = $cashInHandOpening + $cashInFieldOpening + $pettyCash + $liabilitiesInCash->sum('amount') + $suspenseAccount + $debitIncomeInOfficeCash;
                                $totalCashDebitReceipt = $liabilitiesInCash->sum('amount') + $debitIncomeInOfficeCash;
                                $totalBankDebit = $liabilitiesInBank->sum('amount') + $debitIncomeInFieldCash;
                            @endphp --}}

                            @php
                                // 1. Calculate Total Cash Receipts (including Equity)
                                $totalCashDebitReceipt = $liabilitiesInCash->sum('amount') + 
                                                        $equityInCashReceived->sum('amount') + 
                                                        $debitIncomeInOfficeCash + 
                                                        $totalDebitTransferCash;

                                // 2. Calculate Total Bank Receipts (including Equity)
                                // Note: I included $debitIncomeInFieldCash here as it represents your "Bank" income in your loop
                                $totalBankDebit = $liabilitiesInBank->sum('amount') + 
                                                $equityInBankReceived->sum('amount') + 
                                                $debitIncomeInFieldCash + 
                                                $totalDebitTransferBank;

                                // 3. Grand Total Cash (Openings + Receipts + Petty + Suspense)
                                $totalCashDebit = $cashInHandOpening + 
                                                $cashInFieldOpening + 
                                                $pettyCash + 
                                                $suspenseAccount + 
                                                $totalCashDebitReceipt;
                            @endphp

                            

                            <tr class="font-weight-bold bg-light">
                                <td colspan="4" class="text-right">Total Receipts</td>
                                <td class="text-right">{{ number_format($totalCashDebitReceipt, 2) }}</td>
                                <td class="text-right">{{ number_format($totalBankDebit, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>

                            <!-- payment start -->
                            @php
                                // $totalCashCredits = 0;
                                // $totalBankCredits = 0;

                                $deductBalances = function($item) use (&$closingCashInOffice, &$closingCashInField) {
                                    if ($item->account_id == 1) {
                                        $closingCashInOffice -= $item->amount;
                                    } elseif ($item->account_id == 2) {
                                        $closingCashInField -= $item->amount;
                                    }
                                };
                            @endphp

                            {{-- 1. Vendor Advances --}}
                            @foreach ($vendorAdvances as $motherVasselId => $transactions)
                                @php
                                    $vesselName = $transactions->first()->motherVassel->name ?? 'Unknown Vessel';
                                    $amount = $transactions->sum('amount');
                                    $totalCashCredits += $amount;
                                    $closingCashInField -= $amount;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <span class="badge badge-warning">Advance</span> Vendors - {{ $vesselName }} 
                                                <small class="text-muted ml-1">({{ $transactions->count() }} Trips)</small>
                                            </span>
                                            {{-- Account Type Badge --}}
                                            <span class="badge badge-info px-2 py-1" style="background-color: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb;">
                                                {{ $transactions->first()->account->type ?? 'Field Cash' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center small">{{ $transactions->first()->tran_id ?? '' }}</td>
                                    <td colspan="3"></td>
                                    <td class="text-right font-weight-bold">{{ number_format($amount, 2) }}</td>
                                    <td></td>
                                </tr>
                            @endforeach

                            {{-- 2. Expenses --}}
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <span class="text-danger font-weight-bold">Expense:</span> {{ $expense->chartOfAccount->account_name ?? '' }} {{ $expense->vendor ? '- '.$expense->vendor->name : '' }}
                                                <div class="small text-muted">{{ $expense->note }} {{ $expense->description ? '- '.$expense->description : '' }}</div>
                                            </div>
                                            <span class="badge px-2 py-1" style="background-color: #f8f9fa; color: #333; border: 1px solid #ddd;">
                                                {{ $expense->account->type ?? '' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center small">{{ $expense->tran_id }}</td>
                                    <td colspan="3"></td>
                                    <td class="text-right font-weight-bold">
                                        @if($expense->payment_type === 'Cash')
                                            @php $totalCashCredits += $expense->amount; @endphp
                                            {{ number_format($expense->amount, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        @if($expense->payment_type === 'Bank')
                                            @php $totalBankCredits += $expense->amount; @endphp
                                            {{ number_format($expense->amount, 2) }}
                                        @endif
                                    </td>
                                </tr>
                                @php $deductBalances($expense); @endphp
                            @endforeach

                            {{-- 3. Liabilities & Equity Payments --}}
                            @php
                                $liabilities = $liabilitiesPaymentInCash->concat($liabilitiesPaymentInBank);
                                $equities = $equityPaymentInCash->concat($equityPaymentInBank);
                            @endphp

                            @foreach (['Liability' => $liabilities, 'Equity' => $equities] as $label => $collection)
                                @foreach ($collection as $item)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <span class="text-secondary font-weight-bold">{{ $label }} Pymt:</span> 
                                                    {{ $item->chartOfAccount->account_name ?? '' }}
                                                    <div class="small text-muted">{{ $item->description }}</div>
                                                </div>
                                                <span class="badge px-2 py-1" style="background-color: #f8f9fa; color: #333; border: 1px solid #ddd;">
                                                    {{ $item->account->type ?? '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center small">{{ $item->tran_id }}</td>
                                        <td colspan="3"></td>
                                        <td class="text-right font-weight-bold">
                                            @if($item->payment_type === 'Cash')
                                                @php $totalCashCredits += $item->amount; @endphp
                                                {{ number_format($item->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            @if($item->payment_type === 'Bank')
                                                @php $totalBankCredits += $item->amount; @endphp
                                                {{ number_format($item->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                    @php $deductBalances($item); @endphp
                                @endforeach
                            @endforeach

                            {{-- 4. Assets & Transfers --}}
                            @foreach (['Asset' => $assetsPurchase, 'Transfer' => $creditTransfer] as $type => $collection)
                                @foreach ($collection as $entry)
                                    <tr>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($entry->date)->format('d-m-Y') }}</td>
                                        <td>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><span class="badge badge-secondary">{{ $type }}</span> {{ $entry->description }}</span>
                                                <span class="badge px-2 py-1" style="background-color: #f3e5f5; color: #4a148c; border: 1px solid #e1bee7;">
                                                    {{ $entry->account->type ?? '' }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="text-center small">{{ $entry->tran_id }}</td>
                                        <td colspan="3"></td>
                                        <td class="text-right font-weight-bold">
                                            @if($entry->payment_type === 'Cash')
                                                @php $totalCashCredits += $entry->amount; @endphp
                                                {{ number_format($entry->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right font-weight-bold">
                                            @if($entry->payment_type === 'Bank')
                                                @php $totalBankCredits += $entry->amount; @endphp
                                                {{ number_format($entry->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                    @php $deductBalances($entry); @endphp
                                @endforeach
                            @endforeach

                            {{-- Total Payments Row --}}
                            <tr class="bg-light font-weight-bold">
                                <td colspan="6" class="text-right text-uppercase small">Total Payments</td>
                                <td class="text-right border-top border-dark">{{ number_format($totalCashCredits, 2) }}</td>
                                <td class="text-right border-top border-dark">{{ number_format($totalBankCredits, 2) }}</td>
                            </tr>
                            <!-- payment end -->

                            <tr class="font-italic">
                                <td class="text-center">{{ $date }}</td>
                                <td>Cash In Hand (Closing Balance)</td>
                                <td colspan="4"></td>
                                <td class="text-right text-info font-weight-bold">{{ number_format($closingCashInOffice, 2) }}</td>
                                <td></td>
                            </tr>
                            <tr class="font-italic">
                                <td class="text-center">{{ $date }}</td>
                                <td>Cash In Field (Closing Balance)</td>
                                <td colspan="4"></td>
                                <td class="text-right text-info font-weight-bold">{{ number_format($closingCashInField, 2) }}</td>
                                <td></td>
                            </tr>

                            @php
                                $netCashCredit = $closingCashInOffice + $closingCashInField + $pettyCash + $suspenseAccount + $totalCashCredits;
                                $netBankCredit = $totalBankCredits; 
                            @endphp

                            <tr class="bg-dark text-white font-weight-bold">
                                <td colspan="4" class="text-center text-uppercase">Grand Total</td>
                                <td class="text-right">{{ number_format($totalCashDebit, 2) }}</td>
                                <td class="text-right">{{ number_format($totalBankDebit, 2) }}</td>
                                <td class="text-right">{{ number_format($netCashCredit, 2) }}</td>
                                <td class="text-right">{{ number_format($netBankCredit, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-5 pt-4 mb-3">
                    @foreach(['Prepared By', 'Checked By', 'Approved By', 'Managing Director'] as $role)
                        <div class="col-3 text-center">
                            <div style="border-top: 1px solid #333; width: 80%; margin: 0 auto;"></div>
                            <p class="mt-2 font-weight-bold small">{{ $role }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(document).ready(function() {
        // Flatpickr Setup
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);

        flatpickr("#kt_datepicker", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
            minDate: "2025-07-20",
            maxDate: yesterday,
            disableMobile: "true"
        });


    });
</script>
@endsection