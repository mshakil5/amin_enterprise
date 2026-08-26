<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debit Voucher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .voucher {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            border: 2px solid #000;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        .header {
            text-align: center;
            font-weight: bold;
        }
        .voucher-title {
            background: #000;
            color: #fff;
            text-align: center;
            padding: 5px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .amount-box {
            text-align: right;
            font-weight: bold;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <button class="btn btn-secondary my-3 print-btn" onclick="window.print()">Print</button>
        <div class="voucher">
            <div class="header">
                <h4>M/S. AMIN ENTERPRISE</h4>
                <p>IMS Momtaz Tower (4th Floor), 1022, Strand Road, Chattogram.<br>
                Phone: 01713-603882, Email: aminent.bd1@gmail.com</p>
            </div>
            @php
                $debitTables = ['Liabilities', 'Expenses', 'Expense'];
                $isDebit = !$data->table_type || in_array($data->table_type, $debitTables);
            @endphp

            @php
                $reverse = $data->reverseTransaction;
            @endphp

            <div class="voucher-title">{{ $isDebit ? 'DEBIT VOUCHER' : 'CREDIT VOUCHER' }}</div>

            <div class="row mb-2">
                <div class="col">Voucher No: <strong>{{$data->tran_id}}</strong></div>
                <div class="col text-end">Date: <strong>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</strong></div>
            </div>
            <div class="mb-2">Client: <strong>{{ $data->client?->name }}</strong></div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">SL</th>
                        <th class="text-center">Particulars</th>
                        <th class="text-center">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td> {{$data->chartOfAccount?->account_name}}  {{$data->note ?? ''}} <br> {{$data->description ?? ''}} </td>
                        <td class="text-end"> {{$data->at_amount}}/-</td>
                    </tr>
                    @if($reverse)
                    <tr>
                        <td class="text-center">2</td>
                        <td> {{$reverse->note ?? $reverse->note ?? 'Reversed'}} </td>
                        <td class="text-end"> -{{$data->at_amount}}/-</td>
                    </tr>
                    @endif
                </tbody>

            </table>


            <div class="amount-box">
                Total: <strong>{{ $data->reverseTransaction ? 0 : $data->at_amount }}/-</strong>
            </div>
            <div class="mt-3">
                <strong>Taka (In Words):</strong> 
                {{ \Rmunate\Utilities\SpellNumber::value($data->reverseTransaction ? 0 : $data->at_amount)->locale('en')->toLetters() }}
            </div>
            {{-- Update the style to support signature styling --}}
            <style>
                .signature-name {
                    font-weight: bold;
                    text-decoration: underline;
                    margin-bottom: 15px;
                    display: inline-block;
                }
                .signature-label {
                    font-size: 14px;
                }
                @media print {
                    .print-btn {
                        display: none;
                    }
                }
            </style>

            <div class="row mt-5 text-center">
                <div class="col">
                    <span class="signature-label">Checked by:</span><br>
                    <span class="signature-name">{{ $data->checked_by ?? ' ' }}</span>
                </div>
                <div class="col">
                    <span class="signature-label">Received by:</span><br>
                    <span class="signature-name">{{ $data->received_by ?? ' ' }}</span>
                </div>
                <div class="col">
                    <span class="signature-label">Prepared by:</span><br>
                    {{-- Show the fetched preparedByName --}}
                    <span class="signature-name">{{ $preparedByName ?? ' ' }}</span>
                </div>
                <div class="col">
                    <span class="signature-label">Approved by:</span><br>
                    <span class="signature-name">{{ $data->approved_by ?? ' ' }}</span>
                </div>
                <div class="col">
                    <span class="signature-label">Proprietor:</span><br>
                    <span class="signature-name">{{ $data->proprietor ?? ' ' }}</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>



