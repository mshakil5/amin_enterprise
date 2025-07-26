<!-- resources/views/admin/accounts/cash_sheet/cashSheetExport.blade.php -->
<table>
    <thead>
        <tr>
            <th colspan="8">M/S AMIN ENTERPRISE</th>
        </tr>
        <tr>
            <th colspan="8">BSRM PROGRAM</th>
        </tr>
        <tr>
            <th colspan="8">Cash Sheet ({{ $date }})</th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Particulars</th>
            <th>Vch No.</th>
            <th>Chq No.</th>
            <th>Debit Cash</th>
            <th>Debit Bank</th>
            <th>Credit Cash</th>
            <th>Credit Bank</th>
        </tr>
    </thead>
    <tbody>
        <!-- Opening Balances -->
        <tr>
            <td>{{ $date }}</td>
            <td>Cash In Hand (Opening Balance)</td>
            <td></td>
            <td></td>
            <td>{{ number_format($cashInHandOpening, 2) }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $date }}</td>
            <td>Cash In Field (Opening Balance)</td>
            <td></td>
            <td></td>
            <td>{{ number_format($cashInFieldOpening, 2) }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $date }}</td>
            <td>Petty Cash (Entertainment)</td>
            <td></td>
            <td></td>
            <td>{{ number_format($pettyCash, 2) }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $date }}</td>
            <td>Suspense Account</td>
            <td></td>
            <td></td>
            <td>{{ number_format($suspenseAccount, 2) }}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>

        <!-- Debit Transfers -->
        @foreach ($debitTransfer as $dtranfer)
        <tr>
            <td>{{ \Carbon\Carbon::parse($dtranfer->date)->format('d-m-Y') }}</td>
            <td>{{ $dtranfer->description ?? '' }}</td>
            <td>{{ $dtranfer->tran_id ?? '' }}</td>
            <td></td>
            <td>
                @if ($dtranfer->payment_type === 'Cash')
                    {{ number_format($dtranfer->amount, 2) }}
                @endif
            </td>
            <td>
                @if ($dtranfer->payment_type === 'Bank')
                    {{ number_format($dtranfer->amount, 2) }}
                @endif
            </td>
            <td></td>
            <td></td>
        </tr>
        @endforeach

        <!-- Liabilities in Cash -->
        @foreach ($liabilitiesInCash as $cashliability)
        <tr>
            <td>{{ \Carbon\Carbon::parse($cashliability->date)->format('d-m-Y') }}</td>
            <td>{{ $cashliability->chartOfAccount->account_name ?? '' }} - {{ $cashliability->description ?? '' }}</td>
            <td>{{ $cashliability->tran_id ?? '' }}</td>
            <td></td>
            <td>
                @if ($cashliability->payment_type === 'Cash')
                    {{ number_format($cashliability->amount, 2) }}
                @endif
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach

        <!-- Liabilities in Bank -->
        @foreach ($liabilitiesInBank as $bankliability)
        <tr>
            <td>{{ \Carbon\Carbon::parse($bankliability->date)->format('d-m-Y') }}</td>
            <td>{{ $bankliability->chartOfAccount->account_name ?? '' }} - {{ $bankliability->description ?? '' }}</td>
            <td>{{ $bankliability->tran_id ?? '' }}</td>
            <td></td>
            <td></td>
            <td>
                @if ($bankliability->payment_type === 'Bank')
                    {{ number_format($bankliability->amount, 2) }}
                @endif
            </td>
            <td></td>
            <td></td>
        </tr>
        @endforeach

        <!-- Total Receipts -->
        <tr>
            <td colspan="4">Total Receipts</td>
            <td>{{ number_format($liabilitiesInCash->sum('amount'), 2) }}</td>
            <td>{{ number_format($liabilitiesInBank->sum('amount'), 2) }}</td>
            <td></td>
            <td></td>
        </tr>

        <!-- Vendor Advances -->
        @foreach ($vendorAdvances as $motherVasselId => $transactions)
            @php
                $motherVasselName = $transactions->first()->motherVassel->name ?? 'Unknown Vessel';
                $totalAmount = $transactions->sum('amount');
            @endphp
            <tr>
                <td>{{ $date }}</td>
                <td>Advance to Vendors - {{ $motherVasselName }} - Trip: {{ $transactions->count() }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ number_format($totalAmount, 2) }}</td>
                <td></td>
            </tr>
        @endforeach

        <!-- Expenses -->
        @foreach ($expenses as $expense)
        <tr>
            <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
            <td>{{ $expense->chartOfAccount->account_name ?? '' }} {{ $expense->note ?? '' }} ({{ $expense->description ?? '' }})</td>
            <td>{{ $expense->tran_id ?? '' }} ({{ $expense->account->type ?? '' }})</td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                @if ($expense->payment_type === 'Cash')
                    {{ number_format($expense->amount, 2) }}
                @endif
            </td>
            <td>
                @if ($expense->payment_type === 'Bank')
                    {{ number_format($expense->amount, 2) }}
                @endif
            </td>
        </tr>
        @endforeach

        <!-- Liabilities Payments in Cash -->
        @foreach ($liabilitiesPaymentInCash as $liability)
        <tr>
            <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
            <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
            <td>{{ $liability->tran_id ?? '' }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                @if ($liability->payment_type === 'Cash')
                    {{ number_format($liability->amount, 2) }}
                @endif
            </td>
            <td>
                @if ($liability->payment_type === 'Bank')
                    {{ number_format($liability->amount, 2) }}
                @endif
            </td>
        </tr>
        @endforeach

        <!-- Liabilities Payments in Bank -->
        @foreach ($liabilitiesPaymentInBank as $liability)
        <tr>
            <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
            <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
            <td>{{ $liability->tran_id ?? '' }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                @if ($liability->payment_type === 'Cash')
                    {{ number_format($liability->amount, 2) }}
                @endif
            </td>
            <td>
                @if ($liability->payment_type === 'Bank')
                    {{ number_format($liability->amount, 2) }}
                @endif
            </td>
        </tr>
        @endforeach

        <!-- Credit Transfers -->
        @foreach ($creditTransfer as $ctranfer)
        <tr>
            <td>{{ \Carbon\Carbon::parse($ctranfer->date)->format('d-m-Y') }}</td>
            <td>{{ $ctranfer->description ?? '' }}</td>
            <td>{{ $ctranfer->tran_id ?? '' }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td>
                @if ($ctranfer->payment_type === 'Cash')
                    {{ number_format($ctranfer->amount, 2) }}
                @endif
            </td>
            <td></td>
        </tr>
        @endforeach

        <!-- Closing Balances and Totals -->
        @php
            $closingCashInOffice = $cashInHandOpening;
            $closingCashInField = $cashInFieldOpening;
            $closingBankInOffice = 0;
            foreach ($debitTransfer as $dtranfer) {
                if ($dtranfer->account_id === 1) {
                    $closingCashInOffice += $dtranfer->amount;
                }
                if ($dtranfer->account_id === 2) {
                    $closingCashInField += $dtranfer->amount;
                }
            }
            foreach ($liabilitiesInCash as $cashliability) {
                if ($cashliability->account_id === 1) {
                    $closingCashInOffice += $cashliability->amount;
                }
                if ($cashliability->account_id === 2) {
                    $closingCashInField += $cashliability->amount;
                }
            }
            foreach ($liabilitiesInBank as $bankliability) {
                if ($bankliability->account_id === 1 || $bankliability->account_id === 2) {
                    $closingBankInOffice += $bankliability->amount;
                }
            }
            $totalCashCredits = $vendorAdvances->sum(function($transactions) {
                return $transactions->sum('amount');
            });
            foreach ($expenses as $expense) {
                if ($expense->payment_type === 'Cash') {
                    $totalCashCredits += $expense->amount;
                    if ($expense->account_id === 1) {
                        $closingCashInOffice -= $expense->amount;
                    }
                    if ($expense->account_id === 2) {
                        $closingCashInField -= $expense->amount;
                    }
                }
                if ($expense->payment_type === 'Bank') {
                    $closingBankInOffice -= $expense->amount;
                }
            }
            foreach ($liabilitiesPaymentInCash as $liability) {
                $totalCashCredits += $liability->amount;
                if ($liability->account_id === 1) {
                    $closingCashInOffice -= $liability->amount;
                }
                if ($liability->account_id === 2) {
                    $closingCashInField -= $liability->amount;
                }
            }
            foreach ($liabilitiesPaymentInBank as $liability) {
                $totalBankCredits += $liability->amount;
                $closingBankInOffice -= $liability->amount;
            }
            foreach ($creditTransfer as $ctranfer) {
                if ($ctranfer->account_id === 1) {
                    $closingCashInOffice -= $ctranfer->amount;
                }
                if ($ctranfer->account_id === 2) {
                    $closingCashInField -= $ctranfer->amount;
                }
            }
            $totalCashDebit = $cashInHandOpening + $cashInFieldOpening + $pettyCash + $liabilitiesInCash->sum('amount') + $suspenseAccount;
            $totalBankDebit = $liabilitiesInBank->sum('amount');
            $netCashCredit = $closingCashInOffice + $closingCashInField + $pettyCash + $suspenseAccount + $totalCashCredits;
            $netBankCredit = $closingBankInOffice + $totalBankCredits;
        @endphp

        <tr>
            <td colspan="6">Total Payments</td>
            <td>{{ number_format($totalCashCredits, 2) }}</td>
            <td>{{ number_format($totalBankCredits, 2) }}</td>
        </tr>
        <tr>
            <td>{{ $date }}</td>
            <td>Cash In Hand (Closing Balance)</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ number_format($closingCashInOffice, 2) }}</td>
            <td>{{ number_format($closingBankInOffice, 2) }}</td>
        </tr>
        <tr>
            <td>{{ $date }}</td>
            <td>Cash In Field (Closing Balance)</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ number_format($closingCashInField, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $date }}</td>
            <td>Petty Cash (Entertainment)</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ number_format($pettyCash, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>{{ $date }}</td>
            <td>Suspense Account</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ number_format($suspenseAccount, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td colspan="4">Total</td>
            <td>{{ number_format($totalCashDebit, 2) }}</td>
            <td>{{ number_format($totalBankDebit, 2) }}</td>
            <td>{{ number_format($netCashCredit, 2) }}</td>
            <td>{{ number_format($netBankCredit, 2) }}</td>
        </tr>
    </tbody>
</table>