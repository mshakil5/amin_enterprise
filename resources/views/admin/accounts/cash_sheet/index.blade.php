@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between">
            <a href="{{ url()->previous() }}" class="btn btn-secondary mb-2">Back</a>
            <button onclick="window.print();" class="btn btn-info mb-2">Print</button>
        </div>

        <div class="row print-area">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-body">
                        <h1 class="text-center">M/S AMIN ENTERPRISE</h1>
                        <h2 class="text-center">BSRM PROGRAM</h2>
                        <h3 class="text-center">Cash Sheet ({{ $date }})</h3>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th  width="10%">Date</th>
                                    <th>Particulars</th>
                                    <th>Vch No.</th>
                                    <th>Cheque No.</th>
                                    <th class="text-center" colspan="2">Debit</th>
                                    <th class="text-center" colspan="2">Credit</th>
                                </tr>
                                <tr>
                                    <th colspan="4"></th>
                                    <th class="text-center">Cash</th>
                                    <th class="text-center">Bank</th>
                                    <th class="text-center">Cash</th>
                                    <th class="text-center">Bank</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Hand (Opening Balance)</td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td class="text-right">{{ number_format($cashInHandOpening, 0) }}</td>
                                    <td class="text-right" colspan="3"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Field (Opening Balance)</td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td class="text-right">{{ number_format($cashInFieldOpening, 0) }}</td>
                                    <td class="text-right" colspan="3"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Petty Cash (Entertainment)</td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td class="text-right">{{ number_format($pettyCash, 0) }}</td>
                                    <td class="text-right" colspan="3"></td>
                                </tr>
                                <tr class="bg-warning">
                                    <td>{{ $date }}</td>
                                    <td>Suspense Account</td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td class="text-right">{{ number_format($suspenseAccount, 0) }}</td>
                                    <td class="text-right" colspan="3"></td>
                                </tr>

                                @foreach ($liabilitiesInCash as $liability)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                    <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
                                    <td width="10%">{{ $liability->tran_id ?? '' }}</td>
                                    <td width="10%"></td>
                                    <td class="text-right">
                                        @if ($liability->payment_type === 'Cash')
                                            {{ number_format($liability->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($liability->payment_type === 'Bank')
                                            {{ number_format($liability->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                @endforeach

                                @foreach ($liabilitiesInBank as $liability)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                    <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
                                    <td width="10%">{{ $liability->tran_id ?? '' }}</td>
                                    <td width="10%"></td>
                                    <td class="text-right">
                                        @if ($liability->payment_type === 'Cash')
                                            {{ number_format($liability->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($liability->payment_type === 'Bank')
                                            {{ number_format($liability->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                @endforeach

                                @php
                                    $totalCashDebit = $cashInHandOpening + $cashInFieldOpening + $pettyCash + $liabilitiesInCash->sum('amount') + $suspenseAccount;
                                    $totalBankDebit = $liabilitiesInBank->sum('amount');
                                @endphp

                                <tr class="font-weight-bold">
                                    <td colspan="4">Total Receipts</td>
                                    <td class="text-right">{{ number_format($liabilitiesInCash->sum('amount'), 0) }}</td>
                                    <td class="text-right">{{ number_format($liabilitiesInBank->sum('amount'), 0) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>

                                

                                @php
                                    $totalCashCredits = 0;
                                    $totalBankCredits = 0;
                                @endphp

                                {{-- Vendor Advances Grouped By Mother Vessel --}}
                                @foreach ($vendorAdvances as $motherVasselId => $transactions)
                                    @php
                                        $motherVasselName = $transactions->first()->motherVassel->name ?? 'Unknown Vessel';
                                        $totalCount = $transactions->count();
                                        $totalAmount = $transactions->sum('amount');
                                        $totalCashCredits += $totalAmount; // All are 'Cash' here
                                    @endphp
                                    <tr>
                                        <td>{{ $date }}</td>
                                        <td>Advance to Vendors - {{ $motherVasselName }} - Trip: {{ $totalCount }}</td>
                                        <td width="10%"></td>
                                        <td width="10%"></td>
                                        <td width="10%"></td>
                                        <td></td>
                                        <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                                        <td width="10%"></td>
                                    </tr>
                                @endforeach

                                {{-- Expenses --}}

                                @foreach ($expenses as $expense)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
                                        <td>{{ $expense->chartOfAccount->account_name ?? '' }} {{ $expense->note ?? '' }} ({{ $expense->description ?? '' }})</td>
                                        <td width="10%">{{ $expense->tran_id ?? '' }}</td>
                                        <td width="10%"></td>
                                        <td width="10%"></td>
                                        <td width="10%"></td>
                                        <td class="text-right">
                                            @if ($expense->payment_type === 'Cash')
                                                @php $totalCashCredits += $expense->amount; @endphp
                                                {{ number_format($expense->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($expense->payment_type === 'Bank')
                                                @php $totalBankCredits += $expense->amount; @endphp
                                                {{ number_format($expense->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Liabilities (Cash) --}}
                                @foreach ($liabilitiesPaymentInCash as $liability)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                        <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
                                        <td width="10%">{{ $liability->tran_id ?? '' }}</td>
                                        <td width="10%"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right">
                                            @if ($liability->payment_type === 'Cash')
                                                @php $totalCashCredits += $liability->amount; @endphp
                                                {{ number_format($liability->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($liability->payment_type === 'Bank')
                                                @php $totalBankCredits += $liability->amount; @endphp
                                                {{ number_format($liability->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Liabilities (Bank) --}}
                                @foreach ($liabilitiesPaymentInBank as $liability)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                        <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
                                        <td width="10%">{{ $liability->tran_id ?? '' }}</td>
                                        <td width="10%"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right">
                                            @if ($liability->payment_type === 'Cash')
                                                @php $totalCashCredits += $liability->amount; @endphp
                                                {{ number_format($liability->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($liability->payment_type === 'Bank')
                                                @php $totalBankCredits += $liability->amount; @endphp
                                                {{ number_format($liability->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Final Total Row --}}
                                <tr class="font-weight-bold">
                                    <td colspan="6" class="text-left">Total Payments</td>
                                    <td class="text-right">{{ number_format($totalCashCredits, 2) }}</td>
                                    <td class="text-right">{{ number_format($totalBankCredits, 2) }}</td>
                                </tr>


                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Hand (Closing Balance)</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Field (Closing Balance)</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Petty Cash (Entertainment)</td>
                                    <td width="10%"></td>
                                    <td class="text-right" colspan="3"></td>
                                    <td class="text-right">{{ number_format($pettyCash, 0) }}</td>
                                    <td width="10%"></td>
                                </tr>

                                <tr class="font-weight-bold">
                                    <td colspan="4">Total</td>
                                    <td class="text-right">{{ number_format($totalCashDebit, 0) }}</td>
                                    <td class="text-right">{{ number_format($totalBankDebit, 0) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right">{{ number_format($totalBankCredits, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row mt-5">
                            <div class="col-md-3 text-center">
                                <p>Prepared By</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <p>Checked By</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <p>Approved By</p>
                            </div>
                            <div class="col-md-3 text-center">
                                <p>Managing Director</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
@endsection