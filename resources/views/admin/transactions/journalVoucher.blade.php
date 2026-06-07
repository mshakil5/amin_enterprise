<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .voucher-wrapper {
            max-width: 860px;
            margin: 30px auto;
            border: 1px solid #ccc;
            padding: 0;
            background: #fff;
        }

        /* ── Header ── */
        .voucher-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 18px 24px 12px;
            border-bottom: 2px solid #000;
        }

        .company-logo-name {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .company-logo img {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }

        .company-name h2 {
            font-size: 22px;
            font-weight: 900;
            margin: 0;
            line-height: 1.2;
        }

        .company-address {
            font-size: 12px;
            color: #333;
            margin-top: 4px;
            line-height: 1.5;
        }

        .voucher-title-block {
            text-align: right;
            min-width: 220px;
        }

        .voucher-title-block h4 {
            font-size: 18px;
            font-weight: bold;
            font-style: italic;
            margin: 0 0 4px;
        }

        .voucher-title-block .meta {
            font-size: 11px;
            color: #333;
            line-height: 1.7;
        }

        .office-copy {
            font-size: 15px;
            font-weight: bold;
            margin-top: 4px;
        }

        /* ── Body ── */
        .voucher-body {
            padding: 16px 24px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1px solid #aaa;
            margin-bottom: 0;
        }

        .info-row {
            display: contents;
        }

        .info-cell {
            padding: 7px 12px;
            border-bottom: 1px solid #aaa;
            border-right: 1px solid #aaa;
            font-size: 13.5px;
        }

        .info-cell:nth-child(even) {
            border-right: none;
        }

        .info-cell .label {
            display: inline-block;
            min-width: 85px;
            color: #444;
        }

        .info-cell strong {
            font-weight: 700;
        }

        .remarks-cell {
            grid-column: 1 / -1;
            border-right: none;
            border-bottom: none;
            min-height: 55px;
        }

        /* ── In Words ── */
        .in-words {
            margin-top: 18px;
            font-size: 14px;
            font-weight: bold;
        }

        /* ── Signatures ── */
        .signatures {
            margin-top: 30px;
        }

        .sig-name {
            font-size: 12px;
            text-align: center;
            margin-bottom: 2px;
        }

        .sig-line {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #888;
            padding-top: 4px;
            font-size: 12px;
        }

        .sig-item {
            text-align: center;
            flex: 1;
        }

        /* ── Footer ── */
        .voucher-footer {
            border-top: 1px solid #ccc;
            padding: 7px 24px;
            font-size: 11px;
            color: #888;
            text-align: center;
            margin-top: 24px;
        }

        @media print {
            .print-btn { display: none; }
            body { background: #fff; }
            .voucher-wrapper { border: none; margin: 0; }
        }
    </style>
</head>
<body>

<div style="max-width:860px; margin:16px auto;">
    <button class="btn btn-secondary mb-3 print-btn" onclick="window.print()">🖨 Print</button>
</div>

<div class="voucher-wrapper">

    {{-- ══ HEADER ══ --}}
    <div class="voucher-header">

        {{-- Left: Logo + Company --}}
        <div class="company-logo-name">
            {{-- Replace src with your actual logo asset --}}
            <div class="company-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                     onerror="this.style.display='none'">
            </div>
            <div>
                <div class="company-name">
                    <h2>M/S. AMIN ENTERPRISE</h2>
                </div>
                <div class="company-address">
                    IMS Momtaz Tower (4th Floor), 1022, Strand Road, Chattogram.<br>
                    Mobile: 01713-603882<br>
                    Email: aminent.bd1@gmail.com
                </div>
            </div>
        </div>

        {{-- Right: Journal Voucher title + meta --}}
        <div class="voucher-title-block">
            <h4>Journal Voucher</h4>
            <div class="meta">
                Print On : {{ now()->format('d-M-Y h:i:s A') }}<br>
                Printed By : {{ auth()->user()->name ?? 'Admin' }}<br>
                Entry On : {{ \Carbon\Carbon::parse($data->created_at)->format('d-M-Y h:i:s A') }}
            </div>
            <div class="office-copy">Office Copy</div>
        </div>
    </div>

    {{-- ══ BODY ══ --}}
    <div class="voucher-body">

        <div class="info-grid">

            {{-- Row 1: Voucher Date | Voucher No --}}
            <div class="info-cell">
                <span class="label">Voucher Date</span>: 
                <strong>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</strong>
            </div>
            <div class="info-cell">
                <span class="label">Voucher No</span>: 
                <strong>{{ $data->tran_id }}</strong>
            </div>

            {{-- Row 2: Type | (blank or extra field) --}}
            <div class="info-cell">
                <span class="label">Type</span>: 
                <strong>{{ $data->type ?? 'GENERAL' }}</strong>
            </div>
            <div class="info-cell">
                {{-- spare cell; add a field if needed --}}
            </div>

            {{-- Row 3: Mode | Amount --}}
            <div class="info-cell">
                <span class="label">Mode</span>: 
                <strong>JOURNAL</strong>
            </div>
            <div class="info-cell">
                <span class="label">Amount</span>: 
                <strong style="font-size:15px;">{{ number_format($data->at_amount, 2) }}/-</strong>
            </div>

            {{-- Row 4: Debited To | Credited To --}}
            <div class="info-cell">
                <span class="label">Debited To</span>: 
                <strong>{{ $data->client?->name ?? $data->chartOfAccount?->account_name }}</strong>
            </div>
            <div class="info-cell">
                <span class="label">Credited To</span>: 
                <strong>{{ $data->account?->name ?? $data->description ?? '—' }}</strong>
            </div>

            {{-- Row 5: Remarks (full width) --}}
            <div class="info-cell remarks-cell">
                <span class="label">Remarks</span>: 
                {{ $data->note ?? $data->description ?? '' }}
            </div>

        </div>{{-- /info-grid --}}

        {{-- In Words --}}
        <div class="in-words">
            In Words : 
            {{ \Rmunate\Utilities\SpellNumber::value($data->at_amount)->locale('en')->toLetters() }} Only.
        </div>

        {{-- Signatures --}}
        <div class="signatures">
            {{-- Prepared by name above the line --}}
            <div class="sig-name">{{ $data->preparedBy ?? '' }}</div>
            <div class="sig-line">
                <div class="sig-item">Received By</div>
                <div class="sig-item">Prepared By/ Checked By</div>
                <div class="sig-item">Accountant</div>
                <div class="sig-item">Dir. Approval/ Managing Director</div>
                <div class="sig-item">Chairman</div>
            </div>
        </div>

    </div>{{-- /voucher-body --}}

    {{-- Footer --}}
    <div class="voucher-footer">
        Powered by- Amin Enterprise
    </div>

</div>{{-- /voucher-wrapper --}}

</body>
</html>