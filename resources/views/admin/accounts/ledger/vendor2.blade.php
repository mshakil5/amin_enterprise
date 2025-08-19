@extends('admin.layouts.admin')

@section('content')

<style>
    @page {
        size: A4;
        margin: 16mm 12mm;
    }

    /* Screen helpers */
    .no-print { display: inline-block; }
    .print-only { display: none !important; }

    /* Table & layout defaults that survive printing */
    .printable table {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: fixed;
    }
    .printable th, .printable td {
        border: 1px solid #000 !important;
        padding: 6px 8px !important;
        vertical-align: middle;
        word-wrap: break-word;
    }
    .printable thead th {
        text-align: center;
        font-weight: 700;
    }

    /* Repeat header rows on each printed page */
    .printable thead { display: table-header-group; }
    .printable tfoot { display: table-footer-group; }

    /* Avoid broken borders/rows across pages */
    .printable tr { page-break-inside: avoid; }

    /* Remove Bootstrap striping/backgrounds for clean print */
    @media print {
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .card, .card-body, .card-header { border: none !important; box-shadow: none !important; }
        .table-striped tbody tr:nth-of-type(odd) { background: transparent !important; }
        .table-bordered { border: 1px solid #000 !important; }

        /* Page breaks between cards when printing all */
        .print-break { page-break-after: always; }
        .print-break:last-child { page-break-after: auto; }
    }

    /* A simple letterhead-style header for print */
    .print-header {
        margin-bottom: 10px;
        padding-bottom: 8px;
        border-bottom: 2px solid #000;
    }
    .print-header .title {
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }
    .print-header .meta {
        font-size: 12px;
        margin: 2px 0 0 0;
    }
</style>


<div class="d-flex justify-content-end gap-2 mb-2 no-print">
    <button class="btn btn-outline-secondary btn-sm" onclick="printAll()">
        Print All
    </button>
</div>








<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div>
        <div>
            <p>Note: Positive balance Payable, Negative Balance Receivable.</p>
        </div>
        <div class="row justify-content-md-center mt-2 d-none">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h4>{{ $vendor->name }} Ledger</h4>
                    </div>
                    <div class="card-body">

                        {{-- Date Filter Form --}}
                        <form method="GET" class="mb-3">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('admin.vendorledger', $id) }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>

                        <table id="dataTransactionsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="d-none">sl</th>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Payment Type</th>
                                    <th>Ref</th>
                                    <th>Transaction Type</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $balance = $totalBalance; @endphp
                                @foreach($data as $item)
                                    <tr>
                                        <td class="d-none">{{ $loop->iteration }}</td>
                                        <td>{{ $item->tran_id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                                        <td>{{ $item->description }}</td>
                                        <td>{{ $item->payment_type }}</td>
                                        <td>{{ $item->ref }}</td>
                                        <td>{{ $item->tran_type }}</td>
                                        @if($item->tran_type === 'Wallet')
                                            <td>{{ $item->amount }}</td>
                                            <td></td>
                                            <td>{{ $balance }}</td>
                                            @php $balance -= $item->amount; @endphp
                                        @elseif(in_array($item->payment_type, ['Cash', 'Fuel', 'Wallet']))
                                            <td></td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $balance }}</td>
                                            @php $balance += $item->amount; @endphp
                                        @else
                                            <td></td><td></td><td>{{ $balance }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        @php
            $openingBalance = $vendor->opening_balance;
        @endphp


        @foreach ($vsequence as $sequence)
        @php
            $printId = 'print-'.($sequence->id ?? $loop->iteration);
        @endphp

        <div class="row justify-content-md-center mt-2 print-break">
            <div class="col-md-12">
                <div class="card card-secondary printable" id="{{ $printId }}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $sequence->unique_id }}</h4>
                        <button class="btn btn-sm btn-primary no-print" onclick="printSection('{{ $printId }}')">
                            Print
                        </button>
                    </div>

                    {{-- PRINT LETTERHEAD (hidden on screen, shown on print) --}}
                    <div class="print-header print-only">
                        <p class="title">
                            {{ config('app.name') }} — Statement
                        </p>
                        <p class="meta">
                            Sequence: <strong>{{ $sequence->unique_id }}</strong>
                            &nbsp;|&nbsp; Printed: <strong>{{ now()->format('Y-m-d H:i') }}</strong>
                        </p>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Mother Vessel</th>
                                    <th class="text-center">Con. No</th>
                                    <th class="text-center">Total Trip</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Scale Charge</th>
                                    <th class="text-center">Grand Total</th>
                                    <th class="text-center">Advance</th>
                                    <th class="text-center">Balance</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    <td colspan="6"><b>Previous Balance</b></td>
                                    <td colspan="3"></td>
                                    <td class="text-right"><b>{{ number_format($openingBalance, 2)}}</b></td>
                                </tr>

                                @php
                                    $sum_total_trip = 0;
                                    $sum_total_qty = 0;
                                    $sum_total_carrying_bill = 0;
                                    $sum_total_scale_fee = 0;
                                    $sum_grand_total = 0;
                                    $sum_total_advance = 0;
                                    $sum_net_amount = 0;
                                @endphp
                                @foreach ($sequence->programDetail as $detail)
                                    @php
                                        $netAmount = ($detail->total_carrying_bill + $detail->total_scale_fee) - $detail->total_advance;
                                        $totalFuelQty = optional($detail->advancePayment)->fuelqty ?? 0;
                                        $openingBalance += $netAmount;

                                        $sum_total_trip += $detail->total_trip;
                                        $sum_total_qty += $detail->total_qty;
                                        $sum_total_carrying_bill += $detail->total_carrying_bill;
                                        $sum_total_scale_fee += $detail->total_scale_fee;
                                        $sum_grand_total += ($detail->total_carrying_bill + $detail->total_scale_fee);
                                        $sum_total_advance += $detail->total_advance;
                                        $sum_net_amount += $netAmount;
                                    @endphp
                                    <tr>
                                        <td>{{ $sequence->created_at ? $sequence->created_at->format('Y-m-d') : '-' }}</td>
                                        <td>{{ optional($detail->motherVassel)->name ?? '-' }}</td>
                                        <td>{{ $detail->consignmentno ?? '-' }}</td>
                                        <td class="text-center">{{ $detail->total_trip }}</td>
                                        <td class="text-right">{{ number_format($detail->total_qty, 2) }}</td>
                                        <td class="text-right">{{ number_format($detail->total_carrying_bill, 2) }}</td>
                                        <td class="text-right">{{ number_format($detail->total_scale_fee, 2) }}</td>
                                        <td class="text-right">{{ number_format($detail->total_carrying_bill + $detail->total_scale_fee, 2) }}</td>
                                        <td class="text-right">{{ number_format($detail->total_advance, 2) }}</td>
                                        <td class="text-right">{{ number_format($netAmount, 2) }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td></td>
                                    <td colspan="2"><b>Total:</b></td>
                                    <td class="text-center"><b>{{ $sum_total_trip }}</b></td>
                                    <td class="text-right"><b>{{ number_format($sum_total_qty, 2) }}</b></td>
                                    <td class="text-right"><b>{{ number_format($sum_total_carrying_bill, 2) }}</b></td>
                                    <td class="text-right"><b>{{ number_format($sum_total_scale_fee, 2) }}</b></td>
                                    <td class="text-right"><b>{{ number_format($sum_grand_total, 2) }}</b></td>
                                    <td class="text-right"><b>{{ number_format($sum_total_advance, 2) }}</b></td>
                                    <td class="text-right"><b>{{ number_format($sum_net_amount, 2) }}</b></td>
                                </tr>

                                @foreach ($sequence->transaction as $transaction)
                                    <tr>
                                        <td>{{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') : '-' }}</td>
                                        <td colspan="5">{{ $transaction->description ?? '-' }} from {{ $transaction->account->type ?? '-' }}
                                            <br>
                                            {{ $transaction->note ?? '-' }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">{{ number_format($transaction->at_amount, 2) }}</td>
                                    </tr>
                                    @php
                                        $openingBalance -= $transaction->at_amount;
                                    @endphp
                                @endforeach

                            </tbody>

                            @php
                                $totals = $sequence->getAdvancePaymentTotals();
                            @endphp
                            <tfoot>
                                <tr>
                                    <td colspan="4"><b>Total fuel qty for {{ $sequence->unique_id }}:</b> ({{ $totals['total_fuelqty'] }} ltr)</td>
                                    <td colspan="4"></td>
                                    <td class="text-right"><b>Closing Balance:</b></td>
                                    <td class="text-right"><b>{{ number_format($openingBalance, 2)}}</b></td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- Optional signature/footer area (print only) --}}
                        <div class="mt-4 print-only" style="display:flex; gap:24px;">
                            <div style="flex:1;">
                                <div style="border-top:1px solid #000; padding-top:6px; font-size:12px;">Prepared By</div>
                            </div>
                            <div style="flex:1;">
                                <div style="border-top:1px solid #000; padding-top:6px; font-size:12px;">Checked By</div>
                            </div>
                            <div style="flex:1;">
                                <div style="border-top:1px solid #000; padding-top:6px; font-size:12px;">Authorized Signature</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</section>
@endsection

@section('script')
<script>
    $(function () {
        $("#dataTransactionsTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"],
            "lengthMenu": [[100, -1, 50, 25], [100, "All", 50, 25]]
        }).buttons().container().appendTo('#dataTransactionsTable_wrapper .col-md-6:eq(0)');
    });

    window.onload = function () {
        window.scrollTo(0, document.body.scrollHeight);
    };
</script>

{{-- Print Script --}}
<script>
    function printSection(sectionId) {
        var printContent = document.getElementById(sectionId).innerHTML;
        var originalContent = document.body.innerHTML;

        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload(); // reload page to restore events
    }
</script>

<script>
    function buildPrintHTML(innerHTML) {
        // Minimal HTML with embedded styles from the page (grabs first <style> block above)
        const styleBlocks = Array.from(document.querySelectorAll('style'))
            .map(s => s.outerHTML).join('\n');

        return `
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Print</title>
${styleBlocks}
</head>
<body>${innerHTML}</body>
</html>`;
    }

    function openPrintWindow(contentNode) {
        const win = window.open('', '_blank');
        if (!win) return alert('Popup blocked. Please allow popups to print.');
        win.document.open();
        win.document.write(buildPrintHTML(contentNode.outerHTML));
        win.document.close();
        win.focus();
        win.onload = function () {
            win.print();
            win.close();
        };
    }

    function printSection(sectionId) {
        const node = document.getElementById(sectionId);
        if (!node) return;
        openPrintWindow(node);
    }

    function printAll() {
        // Clone all printable blocks and join
        const nodes = document.querySelectorAll('.printable');
        if (!nodes.length) return;
        const wrapper = document.createElement('div');
        nodes.forEach((n, i) => {
            const clone = n.cloneNode(true);
            wrapper.appendChild(clone);
        });
        openPrintWindow(wrapper);
    }
</script>
@endsection