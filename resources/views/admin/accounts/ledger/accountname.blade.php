@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Account Names</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <table id="chartOfAccountsTable" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>SL</th>
            <th>Account Name</th>
            <th>Account Head</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chartOfAccounts as $key => $account)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $account->account_name }}</td>
                <td>{{ $account->account_head }}</td>
                <td>
                    @php
                        $url = '';
                        switch ($account->account_head) {
                            case 'Assets': $url = '/admin/ledger/asset-details/'; break;
                            case 'Expenses': $url = '/admin/ledger/expense-details/'; break;
                            case 'Income': $url = '/admin/ledger/income-details/'; break;
                            case 'Liabilities': $url = '/admin/ledger/liability-details/'; break;
                            case 'Equity': $url = '/admin/ledger/equity-details/'; break;
                        }
                    @endphp
                    <a href="{{ url($url . $account->id) }}" class="btn btn-primary btn-sm">View</a>
                </td>
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
$(document).ready(function () {
    $('#chartOfAccountsTable').DataTable({
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        pageLength: 25,
        order: [[1, 'asc']], // sort by Account Name
        buttons: ["copy", "csv", "excel", "pdf", "print"],
        dom: 'Bfrtip', // show buttons on top
    });
});
</script>
@endsection

