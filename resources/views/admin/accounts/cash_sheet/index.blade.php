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
                        <h3 class="text-center">Cash Sheet ({{ date('d-m-Y') }})</h3>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Particulars</th>
                                    <th>Vch No.</th>
                                    <th>Cheque No.</th>
                                    <th class="text-right">Debit</th>
                                    <th class="text-right">Credit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ date('d-m-Y') }}</td>
                                    <td>Cash In Hand (Opening Balance)</td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td class="text-right">{{ number_format($cashInHand, 0) }}</td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ date('d-m-Y') }}</td>
                                    <td>Cash In Field (Opening Balance)</td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td class="text-right">{{ number_format($cashInField, 0) }}</td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ date('d-m-Y') }}</td>
                                    <td>Petty Cash (Entertainment)</td>
                                    <td width="10%"></td>
                                    <td width="10%"></td>
                                    <td class="text-right">{{ number_format($pettyCash, 0) }}</td>
                                    <td class="text-right"></td>
                                </tr>

                                @foreach ($liabilities as $liability)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                                    <td>{{ $liability->chartOfAccount->account_name ?? '' }} - {{ $liability->description ?? '' }}</td>
                                    <td width="10%">{{ $liability->tran_id ?? '' }}</td>
                                    <td width="10%"></td>
                                    <td class="text-right">{{ number_format($liability->amount, 0) }}</td>
                                    <td class="text-right"></td>
                                </tr>
                                @endforeach

                                <tr class="font-weight-bold">
                                    <td colspan="4">Total Receipts</td>
                                    <td class="text-right">{{ number_format($totalReceipts, 0) }}</td>
                                    <td class="text-right"></td>
                                </tr>

                                @foreach ($vendorAdvances as $vendorId => $transactions)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($transactions->first()->date)->format('d-m-Y') }}</td>
                                    <td>Advance to {{ $transactions->first()->vendor->name ?? '' }}</td>
                                    <td width="10%">{{ $transactions->first()->tran_id ?? '' }}</td>
                                    <td width="10%"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">{{ number_format($transactions->sum('amount'), 0) }}</td>
                                </tr>
                                @endforeach
                                
                                @foreach ($expenses as $expense)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
                                    <td>{{ $expense->chartOfAccount->account_name ?? '' }} - {{ $expense->description ?? '' }}</td>
                                    <td width="10%">{{ $expense->tran_id ?? '' }}</td>
                                    <td width="10%"></td>
                                    <td class="text-right"></td>
                                    <td class="text-right">{{ number_format($expense->amount, 0) }}</td>
                                </tr>
                                @endforeach

                                <tr class="font-weight-bold">
                                    <td colspan="4">Total Payments</td>
                                    <td class="text-right"></td>
                                    <td class="text-right">{{ number_format($totalPayments, 0) }}</td>
                                </tr>

                                <tr>
                                    <td>{{ date('d-m-Y') }}</td>
                                    <td>Cash In Hand (Closing Balance)</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($closingCashInHand, 0) }}</td>
                                    <td class="text-right"></td>
                                </tr>
                                <tr>
                                    <td>{{ date('d-m-Y') }}</td>
                                    <td>Cash In Field (Closing Balance)</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{ number_format($closingCashInField, 0) }}</td>
                                    <td class="text-right"></td>
                                </tr>

                                <tr class="font-weight-bold">
                                    <td colspan="4">Grand Total</td>
                                    <td class="text-right">{{ number_format($grandTotalDebit, 0) }}</td>
                                    <td class="text-right">{{ number_format($grandTotalCredit, 0) }}</td>
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