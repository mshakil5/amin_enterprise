@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div>
        <div>
            <p>Note: Positive balance Payable, Negative Balance Receivable. {{ $vendor->opening_balance }}</p>
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

        @foreach ($vsequence as $items)
        @foreach ($items->programDetail as $item)
            @php
                $netAmount = ($item->total_carrying_bill + $item->total_scale_fee) - $item->total_advance;
                $openingBalance += $netAmount;
            @endphp
        @endforeach
        @foreach ($items->transaction as $transaction)
            @php
                $openingBalance -= $transaction->at_amount;
            @endphp
        @endforeach
            
        @endforeach


        @foreach ($vsequence as $sequence)
            <div class="row justify-content-md-center mt-2">
                <div class="col-md-12">
                    <div class="card card-secondary">
                        <div class="card-header">
                            <h4>{{ $sequence->unique_id }}</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Mother Vessel</th>
                                        <th>Con. No</th>
                                        <th>Total Trip</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Scale Charge</th>
                                        <th>Grand Total</th>
                                        <th>Advance</th>
                                        <th>Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="6"><b>Previous Balance</b></td>
                                        <td colspan="3"></td>
                                        <td><b>{{ number_format($openingBalance, 2)}}</b></td>
                                    </tr>
                                    @foreach ($sequence->programDetail as $detail)
                                        @php
                                            $netAmount = ($detail->total_carrying_bill + $detail->total_scale_fee) - $detail->total_advance;
                                            $totalFuelQty = optional($detail->advancePayment)->fuelqty ?? 0;
                                            $openingBalance -= $netAmount;
                                        @endphp
                                        <tr>
                                            <td>{{ $sequence->created_at ? $sequence->created_at->format('Y-m-d') : '-' }}</td>
                                            <td>{{ optional($detail->motherVassel)->name ?? '-' }}</td>
                                            <td>{{ $detail->consignmentno ?? '-' }}</td>
                                            <td>{{ $detail->total_trip }}</td>
                                            <td>{{ number_format($detail->total_qty, 2) }}</td>
                                            <td>{{ number_format($detail->total_carrying_bill, 2) }}</td>
                                            <td>{{ number_format($detail->total_scale_fee, 2) }}</td>
                                            <td>{{ number_format($detail->total_carrying_bill + $detail->total_scale_fee, 2) }}</td>
                                            <td>{{ number_format($detail->total_advance, 2) }}</td>
                                            <td>{{ number_format($netAmount, 2) }}</td>
                                        </tr>
                                    @endforeach

                                    @foreach ($sequence->transaction as $transaction)
                                        <tr>
                                            <td>{{ $transaction->date ? \Carbon\Carbon::parse($transaction->date)->format('Y-m-d') : '-' }}</td>
                                            <td colspan="5">{{ $transaction->description ?? '-' }} from {{ $transaction->account->type ?? '-' }}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ number_format($transaction->at_amount, 2) }}</td>
                                        </tr>
                                        @php
                                            $openingBalance += $transaction->at_amount;
                                        @endphp
                                    @endforeach


                                    @php
                                        $totals = $sequence->getAdvancePaymentTotals();
                                    @endphp
                                    <tr>
                                        <td colspan="4"><b>Total fuel qty for {{ $sequence->unique_id }}</b></td>
                                        <td>{{ number_format($totals['total_fuelqty'], 2) }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="9"><b>Closing Balance:</b></td>
                                        <td><b>{{ number_format($openingBalance, 2)}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
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
</script>
@endsection