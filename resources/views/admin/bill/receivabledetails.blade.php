@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="mb-0">
                <i class="fas fa-file-invoice-dollar mr-2 text-primary"></i>Receivable Details
            </h3>
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        {{-- Summary Info Boxes --}}
        <div class="row mb-3">
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-info">
                    <span class="info-box-icon"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Date</span>
                        <span class="info-box-number" style="font-size:14px">{{ $billReceive->date }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-primary">
                    <span class="info-box-icon"><i class="fas fa-university"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Rcv Type</span>
                        <span class="info-box-number" style="font-size:14px">{{ $billReceive->rcv_type }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-secondary">
                    <span class="info-box-icon"><i class="fas fa-weight"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Qty</span>
                        <span class="info-box-number">{{ $billReceive->qty }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Amount</span>
                        <span class="info-box-number">{{ number_format($billReceive->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-warning">
                    <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Net Amount</span>
                        <span class="info-box-number">{{ number_format($billReceive->net_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg col-md-4 col-sm-6">
                <div class="info-box bg-danger">
                    <span class="info-box-icon"><i class="fas fa-receipt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bills Count</span>
                        <span class="info-box-number">{{ $programDetails->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bill Summary --}}
        <div class="card card-outline card-secondary mb-4">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Bill Summary</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body py-2">
                <div class="row text-center">
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Maintenance</small>
                        <h6 class="mb-0">{{ number_format($billReceive->maintainance, 2) }}</h6>
                    </div>
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Scale Charge</small>
                        <h6 class="mb-0">{{ number_format($billReceive->scale_charge, 2) }}</h6>
                    </div>
                    <div class="col-md-3 border-right">
                        <small class="text-muted">Other Exp</small>
                        <h6 class="mb-0">{{ number_format($billReceive->other_exp, 2) }}</h6>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Other Rcv</small>
                        <h6 class="mb-0">{{ number_format($billReceive->other_rcv, 2) }}</h6>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== LEDGER TABLE ===== --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-table mr-1"></i> Ledger — BSRM Steels Ltd.
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body p-0">

                {{-- Export Buttons --}}
                <div class="px-3 pt-3 pb-2">
                    <button class="btn btn-sm btn-secondary" id="btn-copy">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                    <button class="btn btn-sm btn-success" id="btn-csv">
                        <i class="fas fa-file-csv"></i> CSV
                    </button>
                    <button class="btn btn-sm btn-primary" id="btn-excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button class="btn btn-sm btn-danger" id="btn-pdf">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button class="btn btn-sm btn-dark" id="btn-print">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="ledger-table" class="table table-bordered table-striped table-sm mb-0" style="font-size:12px;">
                        <thead>
                            <tr class="bg-dark text-white text-center">
                                <th style="width:10px">Action</th>
                                <th style="width:70px">Date</th>
                                <th style="width:110px">Details</th>
                                <th style="width:130px">MV</th>
                                <th style="width:90px">Consignment</th>
                                <th style="width:70px">Bill No.</th>
                                <th>Destination</th>
                                <th style="width:45px">Trip</th>
                                <th style="width:65px">Qty</th>
                                <th style="width:80px">Remarks</th>
                                <th style="width:100px">Dr</th>
                                <th style="width:80px">Cr</th>
                                <th style="width:100px">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $balance  = 0;
                                $totalQty = 0;
                                $totalDr  = 0;
                                $totalCr  = 0;
                                $rowNum   = 0;
                            @endphp

                            @foreach ($programDetails as $billNo => $rows)
                                @php
                                    $first   = $rows->first();
                                    $calc    = $billCalculations[$billNo];
                                    $trip    = $calc['trip'];
                                    $qty     = $calc['dest_qty'];
                                    $dr      = $calc['carrying_bill'];
                                    $balance += $dr;
                                    $totalQty += $qty;
                                    $totalDr += $dr;
                                    $rowNum++;

                                    $mv   = optional($first->motherVassel)->name ?? 'N/A';
                                    $dest = optional($first->destination)->name  ?? 'N/A';
                                    $ghat = optional($first->ghat)->name         ?? 'N/A';

                                    // Check if this bill already has a cheque
                                    $hasCheque = isset($billChequeMap[$billNo]);
                                    $chequeInfo = $hasCheque ? $billChequeMap[$billNo] : null;
                                @endphp
                                <tr class="text-center ledger-row {{ $rowNum % 2 == 0 ? 'bg-light' : '' }} {{ $hasCheque ? 'cheque-applied-row' : '' }}" 
                                    data-billno="{{ $billNo }}">
                                    <td>
                                        <input type="checkbox" 
                                               class="form-check-input bill-checkbox" 
                                               data-billno="{{ $billNo }}" 
                                               {{ $hasCheque ? 'checked disabled' : '' }}>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($first->date)->format('d-m-y') }}</td>
                                    <td class="text-left">Scrap Carrying Bill</td>
                                    <td class="text-left font-weight-bold" style="color:#1a7a4a;">{{ $mv }}</td>
                                    <td>{{ $first->consignmentno }}</td>
                                    <td><span class="badge badge-primary">{{ $billNo }}</span></td>
                                    <td class="text-left">Scrap carrying from {{ $ghat }} to {{ $dest }}</td>
                                    <td>{{ $trip }}</td>
                                    <td class="text-right">{{ number_format($qty, 2) }}</td>
                                    <td class="cheque-display">
                                        @if($hasCheque)
                                            <small class="badge badge-success" style="font-size:9px;" 
                                                   title="Cheque: {{ $chequeInfo->cheque_number }} | Bank: {{ $chequeInfo->bank_name ?? 'N/A' }}">
                                                <i class="fas fa-money-check-alt"></i> Chq
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-right font-weight-bold">{{ number_format($dr, 2) }}</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right font-weight-bold text-primary">{{ number_format($balance, 2) }}</td>
                                </tr>
                            @endforeach

                            {{-- ========== CHEQUE CREDIT ROWS ========== --}}
                            @if($chequeDetails->isNotEmpty())
                                @foreach($chequeDetails as $cheque)
                                    @php
                                        $balance -= (float) $cheque->cheque_amount;
                                        $totalCr += (float) $cheque->cheque_amount;
                                        $chequeBillNos = json_decode($cheque->bill_nos, true) ?? [];
                                    @endphp
                                    <tr class="text-center cheque-credit-row" data-cheque-id="{{ $cheque->id }}" style="background-color:#e8f5e9;">
                                        <td>
                                            <button type="button" class="btn btn-link p-0 text-danger view-cheque-btn" 
                                                    data-id="{{ $cheque->id }}" title="View Cheque Details" style="font-size:11px;">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($cheque->cheque_date)->format('d-m-y') }}</td>
                                        <td class="text-left font-weight-bold text-success">Cheque Payment</td>
                                        <td colspan="2" class="text-left">
                                            <small style="font-size:10px;">
                                                Chq: <strong>{{ $cheque->cheque_number }}</strong>
                                                @if($cheque->bank_name)
                                                    | {{ $cheque->bank_name }}
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted" style="font-size:9px;">
                                                {{ count($chequeBillNos) }} bill(s)
                                            </small>
                                        </td>
                                        <td class="text-left" colspan="2">
                                            <small class="text-muted" style="font-size:9px;">
                                                {{ implode(', ', $chequeBillNos) }}
                                            </small>
                                        </td>
                                        <td></td>
                                        <td>
                                            @if($cheque->document_path)
                                                <a href="{{ asset($cheque->document_path) }}" target="_blank" 
                                                   class="btn btn-link p-0" style="font-size:10px;" title="View Document">
                                                    <i class="fas fa-paperclip text-primary"></i> Doc
                                                </a>
                                            @endif
                                        </td>
                                        <td class="text-right">-</td>
                                        <td class="text-right font-weight-bold text-danger">{{ number_format($cheque->cheque_amount, 2) }}</td>
                                        <td class="text-right font-weight-bold text-primary">{{ number_format($balance, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif

                        </tbody>
                        <tfoot>
                            <tr class="bg-warning font-weight-bold text-center">
                                <td class="text-center"></td>
                                <td colspan="7" class="text-right">Grand Total</td>
                                <td class="text-right">{{ number_format($totalQty, 2) }}</td>
                                <td></td>
                                <td class="text-right">{{ number_format($totalDr, 2) }}</td>
                                <td class="text-right text-danger">{{ number_format($totalCr, 2) }}</td>
                                <td class="text-right text-primary">{{ number_format($balance, 2) }}</td>
                            </tr>
                            <tr class="font-weight-bold text-center">
                                <td colspan="11" class="text-right">
                                    <span id="selected-count" class="text-muted" style="font-size:11px;"></span>
                                </td>
                                <td colspan="2" class="text-center">
                                    <button id="btn-add-cheque" class="btn btn-danger btn-sm" disabled>
                                        <i class="fas fa-money-check-alt mr-1"></i> Add Cheque Number
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- ========== CHEQUE MODAL (Add/Edit) ========== --}}
                <div class="modal fade" id="chequeModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-money-check-alt mr-2"></i>Add Cheque Details
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="chequeForm" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="bill_receive_id" value="{{ $billReceive->id }}">

                                    <div class="alert alert-info py-2" style="font-size:12px;">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Selected Bills:</strong>
                                        <span id="selected-bills-display"></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">
                                                    Cheque Number <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="cheque_number" id="cheque_number"
                                                       class="form-control form-control-sm" placeholder="Enter cheque number" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">
                                                    Cheque Date <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" name="cheque_date" id="cheque_date"
                                                       class="form-control form-control-sm" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">Bank Name</label>
                                                <input type="text" name="bank_name" id="bank_name"
                                                       class="form-control form-control-sm" placeholder="Enter bank name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">
                                                    Cheque Amount <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" name="cheque_amount" id="cheque_amount"
                                                       class="form-control form-control-sm" placeholder="0.00" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">Upload Document</label>
                                                <div class="custom-file custom-file-sm">
                                                    <input type="file" name="cheque_document" id="cheque_document"
                                                           class="custom-file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                                    <label class="custom-file-label" for="cheque_document" style="font-size:11px;">
                                                        Choose file (PDF, JPG, PNG, DOC)
                                                    </label>
                                                </div>
                                                <small class="text-muted" style="font-size:10px;">Max file size: 5MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Document Preview --}}
                                    <div id="document-preview" class="mt-2 d-none">
                                        <div class="card">
                                            <div class="card-header py-1 px-2 bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="font-weight-bold" style="font-size:11px;">
                                                        <i class="fas fa-file mr-1"></i>Document Preview
                                                    </small>
                                                    <button type="button" class="btn btn-link text-danger p-0" id="remove-document" style="font-size:11px;">
                                                        <i class="fas fa-times"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <img id="preview-image" class="img-thumbnail" style="max-height:150px; display:none;">
                                                <div id="preview-file" class="d-none">
                                                    <i class="fas fa-file-pdf text-danger fa-2x mr-2"></i>
                                                    <span id="file-name" style="font-size:11px;"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="bill_nos" id="bill_nos" value="">
                                    <div id="existing-cheque-info" class="d-none">
                                        <input type="hidden" name="cheque_id" id="cheque_id" value="">
                                    </div>
                                </div>
                                <div class="modal-footer py-2">
                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                        <i class="fas fa-times mr-1"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm" id="btn-save-cheque">
                                        <i class="fas fa-save mr-1"></i>Save Cheque Details
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ========== CHEQUE VIEW MODAL (Read-only) ========== --}}
                <div class="modal fade" id="chequeViewModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-money-check-alt mr-2"></i>Cheque Details
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="cheque-view-body" style="font-size:13px;">
                                {{-- Filled via AJAX --}}
                            </div>
                            <div class="modal-footer py-2">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-danger btn-sm" id="btn-delete-cheque">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



                {{-- ========== ALL LEDGER: CHEQUE ADD/EDIT MODAL ========== --}}
                <div class="modal fade" id="allChequeModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-money-check-alt mr-2"></i>Add Cheque Details
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="allChequeForm" enctype="multipart/form-data">
                                <div class="modal-body">
                                    <input type="hidden" name="bill_receive_id" id="all_bill_receive_id" value="">

                                    <div class="alert alert-info py-2" style="font-size:12px;">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        <strong>Selected Bills:</strong>
                                        <span id="all-selected-bills-display"></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">
                                                    Cheque Number <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" name="cheque_number" id="all_cheque_number"
                                                    class="form-control form-control-sm" placeholder="Enter cheque number" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">
                                                    Cheque Date <span class="text-danger">*</span>
                                                </label>
                                                <input type="date" name="cheque_date" id="all_cheque_date"
                                                    class="form-control form-control-sm" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">Bank Name</label>
                                                <input type="text" name="bank_name" id="all_bank_name"
                                                    class="form-control form-control-sm" placeholder="Enter bank name">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">
                                                    Cheque Amount <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" name="cheque_amount" id="all_cheque_amount"
                                                    class="form-control form-control-sm" placeholder="0.00" step="0.01" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="font-weight-bold" style="font-size:12px;">Upload Document</label>
                                                <div class="custom-file custom-file-sm">
                                                    <input type="file" name="cheque_document" id="all_cheque_document"
                                                        class="custom-file-input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                                    <label class="custom-file-label" for="all_cheque_document" style="font-size:11px;">
                                                        Choose file (PDF, JPG, PNG, DOC)
                                                    </label>
                                                </div>
                                                <small class="text-muted" style="font-size:10px;">Max file size: 5MB</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="all-document-preview" class="mt-2 d-none">
                                        <div class="card">
                                            <div class="card-header py-1 px-2 bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="font-weight-bold" style="font-size:11px;">
                                                        <i class="fas fa-file mr-1"></i>Document Preview
                                                    </small>
                                                    <button type="button" class="btn btn-link text-danger p-0"
                                                            id="all-remove-document" style="font-size:11px;">
                                                        <i class="fas fa-times"></i> Remove
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body p-2">
                                                <img id="all-preview-image" class="img-thumbnail" style="max-height:150px; display:none;">
                                                <div id="all-preview-file" class="d-none">
                                                    <i class="fas fa-file-pdf text-danger fa-2x mr-2"></i>
                                                    <span id="all-file-name" style="font-size:11px;"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="bill_nos" id="all_bill_nos" value="">
                                    <div id="all-existing-cheque-info" class="d-none">
                                        <input type="hidden" name="cheque_id" id="all_cheque_id" value="">
                                    </div>
                                </div>
                                <div class="modal-footer py-2">
                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                                        <i class="fas fa-times mr-1"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-sm" id="all-btn-save-cheque">
                                        <i class="fas fa-save mr-1"></i>Save Cheque Details
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ========== ALL LEDGER: CHEQUE VIEW MODAL ========== --}}
                <div class="modal fade" id="allChequeViewModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-money-check-alt mr-2"></i>Cheque Details
                                </h5>
                                <button type="button" class="close text-white" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="all-cheque-view-body" style="font-size:13px;"></div>
                            <div class="modal-footer py-2">
                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-danger btn-sm" id="all-btn-delete-cheque">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>

        {{-- Grand Total Boxes --}}
        @php $all = $programDetails->flatten(); @endphp
        <div class="card card-dark">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-calculator mr-1"></i> Grand Total</h3>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h5>{{ $all->sum('dest_qty') }}</h5>
                                <p>Total Dest Qty</p>
                            </div>
                            <div class="icon"><i class="fas fa-weight"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('carrying_bill'), 2) }}</h5>
                                <p>Total Carrying Bill</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-invoice"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('transportcost'), 2) }}</h5>
                                <p>Total Transport Cost</p>
                            </div>
                            <div class="icon"><i class="fas fa-truck"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('advance'), 2) }}</h5>
                                <p>Total Advance</p>
                            </div>
                            <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('scale_fee'), 2) }}</h5>
                                <p>Total Scale Fee</p>
                            </div>
                            <div class="icon"><i class="fas fa-balance-scale"></i></div>
                        </div>
                    </div>
                    <div class="col-lg col-md-4 col-sm-6 mb-2">
                        <div class="small-box {{ $all->sum('due') < 0 ? 'bg-danger' : 'bg-success' }}">
                            <div class="inner">
                                <h5>{{ number_format($all->sum('due'), 2) }}</h5>
                                <p>Total Due</p>
                            </div>
                            <div class="icon"><i class="fas fa-balance-scale-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@section('script')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
 $(document).ready(function() {

    let selectedBillNos = [];
    let currentViewChequeId = null;

    // =============================================
    // FIX: Bind to #ledger-table instead of document
    // DataTables blocks events from bubbling to document
    // =============================================

    // Checkbox change
    $('#ledger-table').on('change', '.bill-checkbox:not(:disabled)', function() {
        const billNo = $(this).data('billno');

        if ($(this).is(':checked')) {
            if (!selectedBillNos.includes(billNo)) {
                selectedBillNos.push(billNo);
            }
        } else {
            selectedBillNos = selectedBillNos.filter(b => b !== billNo);
        }

        updateSelectedCount();
        updateButtonState();
    });

    // View cheque button — FIXED
    $('#ledger-table').on('click', '.view-cheque-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const chequeId = $(this).data('id');
        currentViewChequeId = chequeId;

        $.ajax({
            url: '{{ route("cheque.view") }}',
            type: 'POST',
            data: { 
                cheque_id: chequeId, 
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                const c = response.cheque;
                let html = '<table class="table table-sm table-bordered mb-0">';
                html += '<tr><th style="width:35%">Cheque Number</th><td><strong>' + c.cheque_number + '</strong></td></tr>';
                html += '<tr><th>Cheque Date</th><td>' + c.cheque_date + '</td></tr>';
                html += '<tr><th>Bank Name</th><td>' + (c.bank_name || 'N/A') + '</td></tr>';
                html += '<tr><th>Amount</th><td class="font-weight-bold text-danger">' + parseFloat(c.cheque_amount).toLocaleString('en-IN', {minimumFractionDigits:2}) + '</td></tr>';
                html += '<tr><th>Bills</th><td>' + c.bill_nos.join(', ') + '</td></tr>';
                
                if (c.document_path) {
                    html += '<tr><th>Document</th><td><a href="' + c.document_path + '" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fas fa-download mr-1"></i>' + (c.document_name || 'View Document') + '</a></td></tr>';
                }
                
                html += '<tr><th>Created At</th><td>' + c.created_at + '</td></tr>';
                html += '</table>';
                
                $('#cheque-view-body').html(html);
                $('#chequeViewModal').modal('show');
            },
            error: function() {
                showToast('Error loading cheque details', 'error');
            }
        });
    });

    // Add Cheque button — bind to tfoot (outside DataTables body)
    $('tfoot').on('click', '#btn-add-cheque', function() {
        if (selectedBillNos.length === 0) {
            showToast('Please select at least one bill', 'warning');
            return;
        }
        checkExistingCheque(selectedBillNos);
    });

    // Delete cheque — bind to modal (outside table)
    $('#chequeViewModal').on('click', '#btn-delete-cheque', function() {
        if (!currentViewChequeId) return;
        if (!confirm('Are you sure you want to delete this cheque entry?')) return;

        $.ajax({
            url: '{{ route("cheque.delete") }}',
            type: 'POST',
            data: { 
                cheque_id: currentViewChequeId, 
                _token: '{{ csrf_token() }}', 
                _method: 'DELETE' 
            },
            success: function(response) {
                showToast(response.message || 'Cheque deleted successfully!', 'success');
                $('#chequeViewModal').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            },
            error: function(xhr) {
                showToast(xhr.responseJSON?.message || 'Error deleting cheque', 'error');
            }
        });
    });

    function updateSelectedCount() {
        const count = selectedBillNos.length;
        if (count > 0) {
            $('#selected-count').html('<span class="badge badge-info">' + count + ' bill(s) selected</span>');
        } else {
            $('#selected-count').html('');
        }
    }

    function updateButtonState() {
        const btn = $('#btn-add-cheque');
        if (selectedBillNos.length > 0) {
            btn.prop('disabled', false).removeClass('btn-secondary').addClass('btn-danger');
        } else {
            btn.prop('disabled', true).removeClass('btn-danger').addClass('btn-secondary');
        }
    }

    function checkExistingCheque(billNos) {
        $.ajax({
            url: '{{ route("cheque.check-existing") }}',
            type: 'POST',
            data: { bill_nos: billNos, _token: '{{ csrf_token() }}' },
            success: function(response) {
                resetForm();
                $('#selected-bills-display').text(billNos.join(', '));
                $('#bill_nos').val(JSON.stringify(billNos));

                if (response.exists && response.cheque) {
                    $('#cheque_id').val(response.cheque.id);
                    $('#cheque_number').val(response.cheque.cheque_number);
                    $('#cheque_date').val(response.cheque.cheque_date);
                    $('#bank_name').val(response.cheque.bank_name);
                    $('#cheque_amount').val(response.cheque.cheque_amount);
                    $('#existing-cheque-info').removeClass('d-none');
                    $('#btn-save-cheque').html('<i class="fas fa-edit mr-1"></i>Update Cheque Details');

                    if (response.cheque.document_path) {
                        showExistingDocument(response.cheque.document_path, response.cheque.document_name);
                    }
                } else {
                    $('#btn-save-cheque').html('<i class="fas fa-save mr-1"></i>Save Cheque Details');

                    let totalAmount = 0;
                    billNos.forEach(function(billNo) {
                        const row = $('tr[data-billno="' + billNo + '"]');
                        const drValue = parseFloat(row.find('td:eq(10)').text().replace(/,/g, '')) || 0;
                        totalAmount += drValue;
                    });
                    $('#cheque_amount').val(totalAmount.toFixed(2));
                }

                $('#chequeModal').modal('show');
            },
            error: function() {
                showToast('Error checking existing cheque data', 'error');
            }
        });
    }

    // Document preview
    $('#cheque_document').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 5 * 1024 * 1024) {
                showToast('File size must be less than 5MB', 'warning');
                $(this).val('');
                return;
            }
            $(this).next('.custom-file-label').text(file.name);

            const reader = new FileReader();
            reader.onload = function(event) {
                const ext = file.name.split('.').pop().toLowerCase();
                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
                    $('#preview-image').attr('src', event.target.result).show();
                    $('#preview-file').addClass('d-none');
                } else {
                    $('#preview-image').hide();
                    $('#preview-file').removeClass('d-none');
                    $('#file-name').text(file.name);
                }
                $('#document-preview').removeClass('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    $('#remove-document').on('click', function() {
        $('#cheque_document').val('');
        $('.custom-file-label').text('Choose file (PDF, JPG, PNG, DOC)');
        $('#document-preview').addClass('d-none');
        $('#preview-image').attr('src', '').hide();
        $('#preview-file').addClass('d-none');
    });

    function showExistingDocument(path, fileName) {
        const ext = path.split('.').pop().toLowerCase();
        if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) {
            $('#preview-image').attr('src', path).show();
            $('#preview-file').addClass('d-none');
        } else {
            $('#preview-image').hide();
            $('#preview-file').removeClass('d-none');
            $('#file-name').text(fileName || path.split('/').pop());
        }
        $('#document-preview').removeClass('d-none');
    }

    function resetForm() {
        $('#chequeForm')[0].reset();
        $('#cheque_id').val('');
        $('#existing-cheque-info').addClass('d-none');
        $('#document-preview').addClass('d-none');
        $('#preview-image').hide();
        $('#preview-file').addClass('d-none');
        $('.custom-file-label').text('Choose file (PDF, JPG, PNG, DOC)');
    }

    // Form submission
    $('#chequeForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const billNos = JSON.parse($('#bill_nos').val());

        formData.append('_token', '{{ csrf_token() }}');
        formData.set('bill_nos', billNos.join(','));

        const chequeId = $('#cheque_id').val();
        if (chequeId) {
            formData.append('cheque_id', chequeId);
            formData.append('_method', 'PUT');
        }

        const btn = $('#btn-save-cheque');
        const originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Saving...').prop('disabled', true);

        $.ajax({
            url: chequeId
                ? '{{ route("cheque.update", ":id") }}'.replace(':id', chequeId)
                : '{{ route("cheque.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showToast(response.message || 'Cheque details saved successfully!', 'success');
                $('#chequeModal').modal('hide');
                setTimeout(function() { location.reload(); }, 800);
            },
            error: function(xhr) {
                if (xhr.status === 419) {
                    showToast('Session expired. Please refresh the page.', 'error');
                    return;
                }
                let errors = '';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    Object.keys(xhr.responseJSON.errors).forEach(function(key) {
                        errors += xhr.responseJSON.errors[key].join(', ') + '\n';
                    });
                } else {
                    errors = xhr.responseJSON?.message || 'Error saving cheque details';
                }
                showToast(errors, 'error');
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    function showToast(message, type) {
        type = type || 'info';
        const icons = { success:'fas fa-check-circle', error:'fas fa-exclamation-circle', warning:'fas fa-exclamation-triangle', info:'fas fa-info-circle' };
        const bg = { success:'bg-success', error:'bg-danger', warning:'bg-warning', info:'bg-info' };
        const t = '<div class="toast-container position-fixed top-0 right-0 p-3" style="z-index:9999;"><div class="toast show ' + bg[type] + ' text-white" role="alert" style="min-width:300px;"><div class="toast-header ' + bg[type] + ' text-white border-0" style="font-size:12px;"><i class="' + icons[type] + ' mr-2"></i><strong class="mr-auto">' + type.charAt(0).toUpperCase() + type.slice(1) + '</strong><button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button></div><div class="toast-body" style="font-size:12px;">' + message + '</div></div></div>';
        $('body').append(t);
        setTimeout(function() { $('.toast-container').remove(); }, 4000);
    }

});
</script>


@endsection