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
        <td class="text-right">
            {{ $transfer->payment_type === 'Cash' ? number_format($transfer->amount, 2) : '' }}
        </td>
        <td class="text-right">
            {{ $transfer->payment_type === 'Bank' ? number_format($transfer->amount, 2) : '' }}
        </td>
        <td colspan="2"></td>
    </tr>
    @php $updateBalances($transfer); @endphp
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
            <td class="text-right">
                {{ $liability->payment_type === 'Cash' ? number_format($liability->amount, 2) : '' }}
            </td>
            <td class="text-right">
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
            <td class="text-right">
                {{ $equity->payment_type === 'Cash' ? number_format($equity->amount, 2) : '' }}
            </td>
            <td class="text-right">
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
        <td class="text-right">
            {{ $income->payment_type === 'Cash' ? number_format($income->amount, 2) : '' }}
        </td>
        <td class="text-right">
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