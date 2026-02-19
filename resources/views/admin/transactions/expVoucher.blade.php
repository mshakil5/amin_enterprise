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
            <div class="mb-2"><strong>{{ $data->client?->name }}:</strong> {{$data->chartOfAccount?->account_name}} </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>SL</th>
                        <th>Particulars</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>{{$data->note ?? ''}} <br> {{$data->description ?? ''}} </td>
                        <td class="text-end"> {{$data->at_amount}}/-</td>
                    </tr>
                    @if($reverse)
                    <tr>
                        <td>2</td>
                        <td> {{$reverse->note ?? $reverse->note ?? 'Reversed'}} </td>
                        <td class="text-end"> -{{$data->at_amount}}/-</td>
                    </tr>
                    @endif
                </tbody>

            </table>


            @php
              use Rmunate\Utilities\SpellNumber;
              
              $rawAmount = SpellNumber::value($data->at_amount)
                  ->locale('en')
                  ->toLetters();

              $textAmont = str_ireplace(' and zero', '', $rawAmount);
              
              $inWords = ucwords($textAmont) . ' (Taka Only)';
            @endphp


            <hr>
            <div class="amount-box">
                Total: <strong>{{ $data->reverseTransaction ? 0 : $data->at_amount }}/-</strong>
            </div>
            <div class="mt-3">
                <strong>Taka (In Words):</strong> 
                {{ \Rmunate\Utilities\SpellNumber::value($data->reverseTransaction ? 0 : $data->at_amount)->locale('en')->toLetters() }}
            </div>
            <div class="row mt-4">
                <div class="col">Checked by: <br> ________</div>
                <div class="col">Received by: <br> ________</div>
                <div class="col">Prepared by: <br> ________</div>
                <div class="col">Approved by: <br> ________</div>
                <div class="col">Proprietor:  <br>________</div>
            </div>
        </div>
    </div>
</body>
</html>
