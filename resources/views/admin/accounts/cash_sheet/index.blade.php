@extends('admin.layouts.admin')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<style>
    /* Styling the Flatpickr Input */
    #kt_datepicker {
        min-width: 220px;
        font-weight: 600;
        color: #495057;
        cursor: pointer;
    }

    /* Customizing the Input Group appearance */
    .input-group-text {
        border-radius: 6px 0 0 6px !important;
        border: 1px solid #ced4da;
    }

    .input-group .btn-primary {
        border-radius: 0 6px 6px 0 !important;
    }

    /* Making the calendar look more modern */
    .flatpickr-calendar {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        border: none !important;
    }


    /* Styling for the Flatpickr calendar specifically */
    .flatpickr-day.flatpickr-disabled, 
    .flatpickr-day.flatpickr-disabled:hover {
        background: #f8f9fa !important;
        color: #dee2e6 !important;
        cursor: not-allowed !important;
        border-color: transparent !important;
    }

    /* Highlight the current selection */
    .flatpickr-day.selected {
        background: #007bff !important;
        border-color: #007bff !important;
    }

    /* Ensure the input looks professional */
    #kt_datepicker.flatpickr-input {
        min-width: 200px;
        font-weight: 500;
        color: #333;
    }



</style>


<section class="content pt-3">
    <div class="container-fluid">

        
        <div class="page-header mb-4 pb-3 border-bottom">
            <div class="d-flex flex-wrap justify-content-between align-items-center">


                <div class="search-section">
                    <form action="{{ route('admin.cashSheet.Search')}}" method="POST" class="form-inline">
                        @csrf
                        <div class="input-group shadow-sm border-radius-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0">
                                    <i class="fas fa-history text-secondary"></i>
                                </span>
                            </div>
                            <input type="text" name="searchDate" id="kt_datepicker"
                                class="form-control border-left-0 bg-white" 
                                value="{{ $date }}" 
                                placeholder="Select Past Date" 
                                readonly required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary px-4 shadow-none">
                                    <i class="fas fa-search mr-1"></i> View Sheet
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted ml-2 d-none d-md-block">
                            Available from July 20, 2025
                        </small>
                    </form>
                </div>




                <div class="action-section mt-md-0 mt-3">
                    <div class="btn-group" role="group">
                        <button onclick="window.print();" class="btn btn-outline-secondary">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                        <button id="customExcelButton" class="btn btn-outline-success">
                            <i class="fas fa-file-excel mr-1"></i> Export Excel
                        </button>
                    </div>
                </div>
            </div>
        </div>


    </div>
</section>


<section class="content pt-3">
    <div class="container-fluid">



        <div class="row print-area">
            <div class="col-12">
                <div class="card card-secondary">
                    <div class="card-body">
                        <h1 class="text-center">M/S AMIN ENTERPRISE</h1>
                        <h2 class="text-center">BSRM PROGRAM</h2>
                        <h3 class="text-center">Cash Sheet ({{ $date }})</h3>

                        <table class="table table-bordered" id="cashSheetTable">
                            <thead>
                                <tr>
                                    <th style="width: 10%" >Date</th>
                                    <th>Particulars</th>
                                    <th>Vch No.</th>
                                    <th>Chq No.</th>
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
                                $closingBankInOffice = 0;
                            @endphp
                            <tbody>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Hand (Opening Balance)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right text-bold text-success">{{ number_format($cashInHandOpening, 2) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Field (Opening Balance)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right text-bold text-success">{{ number_format($cashInFieldOpening, 2) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Petty Cash (Entertainment)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right">{{ number_format($pettyCash, 2) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr class="bg-warning">
                                    <td>{{ $date }}</td>
                                    <td>Suspense Account</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right">{{ number_format($suspenseAccount, 2) }}</td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>

                                @foreach ($debitTransfer as $dtranfer)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($dtranfer->date)->format('d-m-Y') }}</td>
                                    <td> {{ $dtranfer->description ?? '' }}</td>
                                    <td >{{ $dtranfer->tran_id ?? '' }}  ({{ $dtranfer->account->type ?? '' }})</td>
                                    <td ></td>
                                    <td class="text-right">
                                        @if ($dtranfer->payment_type === 'Cash')
                                            
                                            {{ number_format($dtranfer->amount, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($dtranfer->payment_type === 'Bank')
                                            {{ number_format($dtranfer->amount, 2) }}
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
                                    <td >{{ $cashliability->tran_id ?? '' }}  ({{ $cashliability->account->type ?? '' }})</td>
                                    <td ></td>
                                    <td class="text-right">
                                        @if ($cashliability->payment_type === 'Cash')
                                            {{ number_format($cashliability->amount, 2) }}
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
                                    <td>{{ $bankliability->chartOfAccount->account_name ?? '' }} 
                                         - {{ $bankliability->description ?? '' }}</td>
                                    <td >{{ $bankliability->tran_id ?? '' }}  ({{ $bankliability->account->type ?? '' }})</td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">
                                        @if ($bankliability->payment_type === 'Bank')
                                            {{ number_format($bankliability->amount, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                @php
                                if ($bankliability->account_id === 1) {
                                    // $closingBankInOffice += $bankliability->amount;
                                }
                                if ($bankliability->account_id === 2) {
                                    // $closingBankInOffice += $bankliability->amount;
                                }
                                @endphp
                                @endforeach



                                {{-- equity receive  --}}
                                @foreach ($equityInCashReceived as $equitycash)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($equitycash->date)->format('d-m-Y') }}</td>
                                        <td>{{ $equitycash->chartOfAccount->account_name ?? '' }} - {{ $equitycash->description ?? '' }}</td>
                                        <td >{{ $equitycash->tran_id ?? '' }}  ({{ $equitycash->account->type ?? '' }})</td>
                                        <td ></td>
                                        <td class="text-right">
                                            @if ($equitycash->payment_type === 'Cash')
                                                {{ number_format($equitycash->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                    </tr>
                                    @php
                                    if ($equitycash->account_id === 1) {
                                        $closingCashInOffice += $equitycash->amount;
                                    }
                                    if ($equitycash->account_id === 2) {
                                        $closingCashInField += $equitycash->amount;
                                    }
                                    @endphp
                                @endforeach

                                

                                @foreach ($equityInBankReceived as $equitybank)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($equitybank->date)->format('d-m-Y') }}</td>
                                    <td>{{ $equitybank->chartOfAccount->account_name ?? '' }} 
                                         - {{ $equitybank->description ?? '' }}</td>
                                    <td >{{ $equitybank->tran_id ?? '' }}  ({{ $equitybank->account->type ?? '' }})</td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">
                                        @if ($equitybank->payment_type === 'Bank')
                                            {{ number_format($equitybank->amount, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                </tr>
                                @php
                                if ($equitybank->account_id === 1) {
                                    // $closingBankInOffice += $bankliability->amount;
                                }
                                if ($equitybank->account_id === 2) {
                                    // $closingBankInOffice += $bankliability->amount;
                                }
                                @endphp
                                @endforeach


                                @php
                                    $debitIncomeInOfficeCash = 0;
                                    $debitIncomeInFieldCash = 0;
                                @endphp




                                @foreach ($incomes as $income)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($income->date)->format('d-m-Y') }}</td>
                                    <td>{{ $income->chartOfAccount->account_name ?? '' }}
                                        @if ($income->tran_type === 'Wallet')
                                            {{$income->vendor->name ?? ''}}
                                        @endif
                                        - {{ $income->description ?? '' }}</td>
                                    <td >{{ $income->tran_id ?? '' }}  ({{ $income->account->type ?? '' }})</td>
                                    <td ></td>
                                    <td class="text-right">
                                        @if ($income->payment_type === 'Cash')
                                            {{ number_format($income->amount, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($income->payment_type === 'Bank')
                                            {{ number_format($income->amount, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right">this</td>
                                    <td class="text-right"></td>
                                </tr>
                                    @php
                                    if ($income->account_id === 1) {
                                        $closingCashInOffice += $income->amount; // this
                                    }
                                    if ($income->account_id === 2) {
                                        $closingCashInField += $income->amount;
                                    }
                                    if ($income->payment_type === 'Cash'){
                                        $debitIncomeInOfficeCash += $income->amount; // this
                                    }
                                    if ($income->payment_type === 'Bank'){
                                        $debitIncomeInFieldCash += $income->amount;
                                    }
                                    @endphp
                                @endforeach

                                @php
                                    $totalCashDebit = $cashInHandOpening + $cashInFieldOpening + $pettyCash + $liabilitiesInCash->sum('amount') + $suspenseAccount + $debitIncomeInOfficeCash;
                                    $totalCashDebitReceipt = $liabilitiesInCash->sum('amount') + $debitIncomeInOfficeCash;
                                    $totalBankDebit = $liabilitiesInBank->sum('amount') + $debitIncomeInFieldCash;
                                @endphp


                                <tr class="font-weight-bold">
                                    <td colspan="4">Total Receipts</td>
                                    <td class="text-right">{{ number_format($totalCashDebitReceipt, 2) }}</td>
                                    <td class="text-right">{{ number_format($liabilitiesInBank->sum('amount') + $debitIncomeInFieldCash, 2) }}</td>
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
                                    
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
                                        <td>{{ $expense->chartOfAccount->account_name ?? '' }} {{ $expense->note ?? '' }} ({{ $expense->description ?? '' }})</td>
                                        <td >{{ $expense->tran_id ?? '' }} ({{ $expense->account->type ?? '' }})</td>
                                        <td ></td>
                                        <td ></td>
                                        <td ></td>
                                        <td class="text-right">
                                            @if ($expense->payment_type === 'Cash')
                                                @php 
                                                
                                                $totalCashCredits += $expense->amount; 

                                                if ($expense->account_id === 1) {
                                                    $closingCashInOffice -= $expense->amount;
                                                }
                                                if ($expense->account_id === 2) {
                                                    $closingCashInField -= $expense->amount;
                                                }
                                                
                                                @endphp
                                                {{ number_format($expense->amount, 2) }}
                                            @endif

                                        </td>
                                        <td class="text-right">
                                            @if ($expense->payment_type === 'Bank')
                                                @php $totalBankCredits += $expense->amount;

                                                $closingBankInOffice -= $expense->amount;

                                                if ($expense->account_id === 1) {
                                                    // $closingCashInOffice -= $expense->amount;
                                                }
                                                if ($expense->account_id === 2) {
                                                    $closingCashInField -= $expense->amount;
                                                }
                                                
                                                @endphp
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
                                        <td >{{ $liability->tran_id ?? '' }} - ({{ $liability->account->type ?? '' }})</td>
                                        <td ></td>
                                        <td class="text-right"> </td>
                                        <td ></td>
                                        <td class="text-right">
                                            @if ($liability->payment_type === 'Cash')
                                                @php $totalCashCredits += $liability->amount; @endphp
                                                {{ number_format($liability->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($liability->payment_type === 'Bank')
                                                @php $totalBankCredits += $liability->amount;
                                                 $closingBankInOffice += $liability->amount;
                                                 @endphp
                                                {{ number_format($liability->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                {{-- Liabilities (Bank) --}}
                                @foreach ($liabilitiesPaymentInBank as $liability)
                                
                                        @php
                                        if ($liability->account_id === 1) {
                                            // $closingBankInOffice -= $liability->amount;
                                        }
                                        if ($liability->account_id === 2) {
                                            $closingCashInField -= $liability->amount;
                                        }
                                        @endphp



                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                        <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
                                        <td >{{ $liability->tran_id ?? '' }} - ({{ $liability->account->type ?? '' }})</td>
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
                                                @php $totalBankCredits += $liability->amount;
                                                //  $closingBankInOffice += $liability->amount;
                                                @endphp
                                                {{ number_format($liability->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach


                                
                                @foreach ($equityPaymentInCash as $equitypayment)
                                        @php
                                        if ($equitypayment->account_id === 1) {
                                            $closingCashInOffice -= $equitypayment->amount;
                                        }
                                        if ($equitypayment->account_id === 2) {
                                            $closingCashInField -= $equitypayment->amount;
                                        }
                                        @endphp
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($equitypayment->date)->format('d-m-Y') }}</td>
                                        <td>{{ $equitypayment->chartOfAccount->account_name ?? '' }} - {{ $equitypayment->description ?? '' }}</td>
                                        <td >{{ $equitypayment->tran_id ?? '' }} - ({{ $equitypayment->account->type ?? '' }})</td>
                                        <td ></td>
                                        <td class="text-right"> </td>
                                        <td ></td>
                                        <td class="text-right">
                                            @if ($equitypayment->payment_type === 'Cash')
                                                @php $totalCashCredits += $equitypayment->amount; @endphp
                                                {{ number_format($equitypayment->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($equitypayment->payment_type === 'Bank')
                                                @php $totalBankCredits += $equitypayment->amount;
                                                 $closingBankInOffice += $equitypayment->amount;
                                                 @endphp
                                                {{ number_format($equitypayment->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                
                                @foreach ($equityPaymentInBank as $eqpbank)
                                
                                        @php
                                        if ($eqpbank->account_id === 1) {
                                            // $closingBankInOffice -= $liability->amount;
                                        }
                                        if ($eqpbank->account_id === 2) {
                                            $closingCashInField -= $eqpbank->amount;
                                        }
                                        @endphp



                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($eqpbank->date)->format('d-m-Y') }}</td>
                                        <td>{{ $eqpbank->chartOfAccount->account_name ?? '' }} - {{ $eqpbank->description ?? '' }}</td>
                                        <td >{{ $eqpbank->tran_id ?? '' }} - ({{ $eqpbank->account->type ?? '' }})</td>
                                        <td ></td>
                                        <td class="text-right"></td>
                                        <td ></td>
                                        <td class="text-right">
                                            @if ($eqpbank->payment_type === 'Cash')
                                                @php $totalCashCredits += $eqpbank->amount; @endphp
                                                {{ number_format($eqpbank->amount, 2) }}
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($eqpbank->payment_type === 'Bank')
                                                @php $totalBankCredits += $eqpbank->amount;
                                                //  $closingBankInOffice += $eqpbank->amount;
                                                @endphp
                                                {{ number_format($eqpbank->amount, 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach





                                @foreach ($creditTransfer as $ctranfer)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($ctranfer->date)->format('d-m-Y') }}</td>
                                    <td> {{ $ctranfer->description ?? '' }}</td>
                                    <td >{{ $ctranfer->tran_id ?? '' }} - ({{ $ctranfer->account->type ?? '' }})</td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">
                                        @if ($ctranfer->payment_type === 'Cash')
                                            {{ number_format($ctranfer->amount, 2) }}
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
                                    <td class="text-right text-bold text-success">{{ number_format($closingCashInOffice, 2) }}</td>
                                    <td class="text-right text-bold text-success"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Cash In Field (Closing Balance)</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right text-bold text-success">{{ number_format($closingCashInField, 2) }}</td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ $date }}</td>
                                    <td>Petty Cash (Entertainment)</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td></td>
                                    <td class="text-right text-bold text-success">{{ number_format($pettyCash, 2) }}</td>
                                    <td></td>
                                </tr>

                                
                                <tr class="bg-warning">
                                    <td>{{ $date }}</td>
                                    <td>Suspense Account</td>
                                    <td ></td>
                                    <td ></td>
                                    <td class="text-right"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">{{ number_format($suspenseAccount, 2) }}</td>
                                    <td class="text-right"></td>
                                </tr>

                                @php
                                    $netCashCredit = $closingCashInOffice + $closingCashInField + $pettyCash + $suspenseAccount + $totalCashCredits;

                                    $netBankCredit = $totalBankCredits; 

                                @endphp

                                <tr class="font-weight-bold">
                                    <td colspan="4">Total</td>
                                    <td class="text-right">{{ number_format($totalCashDebit, 2) }}</td>
                                    <td class="text-right">{{ number_format($totalBankDebit, 2) }}</td>
                                    <td class="text-right">{{ number_format($netCashCredit, 2) }}</td>
                                    <td class="text-right">{{ number_format($netBankCredit, 2) }}</td>
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
<script>
    $(document).ready(function() {
        

        // Optional: Add a custom download button to trigger Excel export
        $('#customExcelButton').on('click', function() {
            var searchDate = $('input[name="searchDate"]').val();

            if (!searchDate) {
                alert('Please select a date.');
                return;
            }

            $.ajax({
                url: "{{ route('admin.cashSheet.export') }}",
                type: "POST",
                data: {
                    searchDate: searchDate,
                    _token: "{{ csrf_token() }}"
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data, status, xhr) {
                    var filename = "Cash_Sheet_" + searchDate + ".xlsx"; // Use searchDate
                    var blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                },
                error: function(xhr, status, error) {
                    console.error('Error:', xhr, status, error); // Log error details
                    alert('Failed to download Excel file. Check the console for details.');
                }
            });
        });
    });
</script>

<script>
    // Calculate yesterday's date
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    flatpickr("#kt_datepicker", {
        dateFormat: "Y-m-d",      // Raw format for the hidden input
        altInput: true,           // Enable the pretty display
        altFormat: "d F Y",       // Display: 4 January 2026
        minDate: "2025-07-20",    // Fixed start date
        maxDate: yesterday,       // Disable today and future
        disableMobile: "true",    // Forces the professional UI on mobile
        locale: {
            firstDayOfWeek: 1     // Starts week on Monday
        }
    });
</script>

@endsection