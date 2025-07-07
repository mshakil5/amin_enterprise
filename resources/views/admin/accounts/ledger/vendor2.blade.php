@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div>
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h4>{{ $accountName }} Ledger</h4>
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