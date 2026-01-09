@php
    $totalCashCredits = 0;
    $totalBankCredits = 0;

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
                    {{ $transactions->first()->account->type ?? 'N/A' }}
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
                    <span class="text-danger font-weight-bold">Expense:</span> {{ $expense->chartOfAccount->account_name ?? 'General' }}
                    <div class="small text-muted">{{ $expense->note }} {{ $expense->description ? '- '.$expense->description : '' }}</div>
                </div>
                <span class="badge px-2 py-1" style="background-color: #f8f9fa; color: #333; border: 1px solid #ddd;">
                    {{ $expense->account->type ?? '' }}
                </span>
            </div>
        </td>
        <td class="text-center small">{{ $expense->tran_id }}</td>
        <td colspan="3"></td>
        <td class="text-right">
            @if($expense->payment_type === 'Cash')
                @php $totalCashCredits += $expense->amount; @endphp
                {{ number_format($expense->amount, 2) }}
            @endif
        </td>
        <td class="text-right">
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
                    <span class="badge px-2 py-1" style="background-color: #fff3e0; color: #e65100; border: 1px solid #ffe0b2;">
                        {{ $item->account->type ?? '' }}
                    </span>
                </div>
            </td>
            <td class="text-center small">{{ $item->tran_id }}</td>
            <td colspan="3"></td>
            <td class="text-right">
                @if($item->payment_type === 'Cash')
                    @php $totalCashCredits += $item->amount; @endphp
                    {{ number_format($item->amount, 2) }}
                @endif
            </td>
            <td class="text-right">
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
            <td class="text-right">
                @if($entry->payment_type === 'Cash')
                    @php $totalCashCredits += $entry->amount; @endphp
                    {{ number_format($entry->amount, 2) }}
                @endif
            </td>
            <td class="text-right">
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