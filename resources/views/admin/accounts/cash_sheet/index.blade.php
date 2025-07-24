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
                                    <th style="width: 10%" >Date</th>
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
                            @php
                                $closingCashInOffice = $cashInHandOpening;
                                $closingCashInField = $cashInFieldOpening;
                            @endphp
                            <tbody>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Hand (Opening Balance)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right">{{ number_format($cashInHandOpening, 0) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Field (Opening Balance)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right">{{ number_format($cashInFieldOpening, 0) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Petty Cash (Entertainment)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right">{{ number_format($pettyCash, 0) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr class="bg-warning">
                                    <td>{{ $date }}</td>
                                    <td>Suspense Account</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right">{{ number_format($suspenseAccount, 0) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>

                                @foreach ($debitTransfer as $dtranfer)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($dtranfer->date)->format('d-m-Y') }}</td>
                                    <td> {{ $dtranfer->description ?? '' }}</td>
                                    <td >{{ $dtranfer->tran_id ?? '' }}</td>
                                    <td ></td>
                                    <td class="text-right">
                                        @if ($dtranfer->payment_type === 'Cash')
                                            
                                            {{ number_format($dtranfer->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($dtranfer->payment_type === 'Bank')
                                            {{ number_format($dtranfer->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>

                                @php
                                if ($dtranfer->account_id === 1) {
                                    $closingCashInOffice += $dtranfer->amount;
                                }
                                if ($dtranfer->account_id === 2) {
                                    $closingCashInField += $dtranfer->amount;
                                }
                                @endphp

                                @endforeach



                                @foreach ($liabilitiesInCash as $cashliability)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($cashliability->date)->format('d-m-Y') }}</td>
                                    <td>{{ $cashliability->chartOfAccount->account_name ?? '' }} - {{ $cashliability->description ?? '' }}</td>
                                    <td >{{ $cashliability->tran_id ?? '' }}</td>
                                    <td ></td>
                                    <td class="text-right">
                                        @if ($cashliability->payment_type === 'Cash')
                                            {{ number_format($cashliability->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                @php
                                if ($cashliability->account_id === 1) {
                                    $closingCashInOffice += $cashliability->amount;
                                }
                                if ($cashliability->account_id === 2) {
                                    $closingCashInField += $cashliability->amount;
                                }
                                @endphp

                                @endforeach

                                @foreach ($liabilitiesInBank as $bankliability)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($bankliability->date)->format('d-m-Y') }}</td>
                                    <td>{{ $bankliability->chartOfAccount->account_name ?? '' }} - {{ $bankliability->description ?? '' }}</td>
                                    <td >{{ $bankliability->tran_id ?? '' }}</td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">
                                        @if ($bankliability->payment_type === 'Bank')
                                            {{ number_format($bankliability->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                @php
                                if ($bankliability->account_id === 1) {
                                    $closingCashInOffice += $bankliability->amount;
                                }
                                if ($bankliability->account_id === 2) {
                                    $closingCashInField += $bankliability->amount;
                                }
                                @endphp
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
                                        $closingCashInField -= $totalAmount;
                                    @endphp
                                    <tr>
                                        <td>{{ $date }}</td>
                                        <td>Advance to Vendors - {{ $motherVasselName }} - Trip: {{ $totalCount }}</td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>
                                        <td></td>
                                        <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                                        <td ></td>
                                    </tr>
                                @endforeach

                                {{-- Expenses --}}

                                @foreach ($expenses as $expense)
                                    
                                    @php
                                    if ($expense->account_id === 1) {
                                        $closingCashInOffice -= $expense->amount;
                                    }
                                    if ($expense->account_id === 2) {
                                        $closingCashInField -= $expense->amount;
                                    }
                                    @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
                                        <td>{{ $expense->chartOfAccount->account_name ?? '' }} {{ $expense->note ?? '' }} ({{ $expense->description ?? '' }})</td>
                                        <td >{{ $expense->tran_id ?? '' }}</td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>
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
                                        @php
                                        if ($liability->account_id === 1) {
                                            $closingCashInOffice -= $liability->amount;
                                        }
                                        if ($liability->account_id === 2) {
                                            $closingCashInField -= $liability->amount;
                                        }
                                        @endphp



                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                        <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
                                        <td >{{ $liability->tran_id ?? '' }}</td>
                                        <td ></td>
                                        <td class="text-right"></td>
                                        <td ></td>
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
                                
                                        @php
                                        if ($liability->account_id === 1) {
                                            $closingCashInOffice -= $liability->amount;
                                        }
                                        if ($liability->account_id === 2) {
                                            $closingCashInField -= $liability->amount;
                                        }
                                        @endphp



                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                        <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
                                        <td >{{ $liability->tran_id ?? '' }}</td>
                                        <td ></td>
                                        <td class="text-right"></td>
                                        <td ></td>
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

                                @foreach ($creditTransfer as $ctranfer)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($ctranfer->date)->format('d-m-Y') }}</td>
                                    <td> {{ $ctranfer->description ?? '' }}</td>
                                    <td >{{ $ctranfer->tran_id ?? '' }}</td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">
                                        @if ($ctranfer->payment_type === 'Cash')
                                            {{ number_format($ctranfer->amount, 0) }}
                                        @endif
                                    </td>
                                    <td class="text-right"></td>
                                </tr>
                                @php
                                if ($ctranfer->account_id === 1) {
                                    $closingCashInOffice -= $ctranfer->amount;
                                }
                                if ($ctranfer->account_id === 2) {
                                    $closingCashInField -= $ctranfer->amount;
                                }
                                @endphp
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
                                    <td class="text-right text-bold text-success">{{ number_format($closingCashInOffice, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Field (Closing Balance)</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right"></td>
                                    <td class="text-right text-bold text-success">{{ number_format($closingCashInField, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Petty Cash (Entertainment)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right text-bold text-success">{{ number_format($pettyCash, 0) }}</td>
                                </tr>

                                
                                <tr class="bg-warning">
                                    <td>{{ $date }}</td>
                                    <td>Suspense Account</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">{{ number_format($suspenseAccount, 0) }}</td>
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