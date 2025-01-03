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
                                    <th>Assets</th>
                                    <th>Expenses</th>
                                    <th>Income</th>
                                    <th>Liabilities</th>
                                    <th>Equity</th>
                                </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td>
                                            @foreach($chartOfAccounts as $asset)
                                                @if($asset->account_head == 'Assets')   
                                                    <a href="{{ url('/admin/ledger/asset-details/' . $asset->id) }}" class="btn btn-block btn-default btn-xs">{{ $asset->account_name }}</a>
                                                @endif  
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($chartOfAccounts as $expense)
                                                @if($expense->account_head == 'Expenses')   
                                                    <a href="{{ url('/admin/ledger/expense-details/' . $expense->id) }}" class="btn btn-block btn-default btn-xs">{{ $expense->account_name }}</a>
                                                @endif  
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($chartOfAccounts as $income)
                                                @if($income->account_head == 'Income')   
                                                    <a href="{{ url('/admin/ledger/income-details/' . $income->id) }}" class="btn btn-block btn-default btn-xs">{{ $income->account_name }}</a>
                                                @endif  
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($chartOfAccounts as $liability)
                                                @if($liability->account_head == 'Liabilities')   
                                                    <a href="{{ url('/admin/ledger/liability-details/' . $liability->id) }}" class="btn btn-block btn-default btn-xs">{{ $liability->account_name }}</a>
                                                @endif  
                                            @endforeach
                                            <hr>
                                            

                                        </td>
                                        <td>
                                            @foreach($chartOfAccounts as $equity)
                                                @if($equity->account_head == 'Equity')   
                                                    <a href="{{ url('/admin/ledger/equity-details/' . $equity->id) }}" class="btn btn-block btn-default btn-xs">{{ $equity->account_name }}</a>
                                                @endif  
                                            @endforeach
                                        </td>
                                    </tr>
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
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"],
        "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
</script>
@endsection
