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
                                
                                $debitIncomeInOfficeCash = 0;
                                $debitIncomeInFieldCash = 0;
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

                                
                                @include('admin.accounts.cash_sheet.partials.receipts')

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

                                


                                @include('admin.accounts.cash_sheet.partials.payments')

                                


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