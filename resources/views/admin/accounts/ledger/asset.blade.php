@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div>
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            @if ($accountName)
                                <h4>{{ $accountName }} Ledger</h4>
                            @else
                                <h4>Account Name Not Found</h4>
                            @endif
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <form method="GET" class="form-inline mb-3">
                            <input type="date" name="start_date" class="form-control mr-2" value="{{ request('start_date') }}">
                            <input type="date" name="end_date" class="form-control mr-2" value="{{ request('end_date') }}">
                            <button type="submit" class="btn btn-primary mr-2">Filter</button>
                            @if(request()->has('start_date') || request()->has('end_date'))
                                <button type="button" class="btn btn-secondary" onclick="window.location='{{ url()->current() }}'">Clear</button>
                            @endif
                        </form>

                        
                        <div class="text-center mb-4 company-name-container">
                            
                        </div>

                        <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Description</th>
                                        <th>Payment Type</th>
                                        <th>Ref</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>                                
                                        <th>Voucher</th>                                
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $balance = $totalBalance;
                                    @endphp

                                    @foreach($data as $index => $data)
                                        <tr>
                                            <td>{{ $data->tran_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                            <td>{{ $data->description }}</td>
                                            <td>{{ $data->payment_type }}</td>
                                            <td>{{ $data->ref }}</td>
                                            @if(in_array($data->tran_type, ['Purchase', 'Payment']))
                                            <td>{{ $data->at_amount }}</td>
                                            <td></td>
                                            <td>{{ $balance }}</td>
                                            @php
                                                $balance = $balance - $data->at_amount;
                                            @endphp
                                            @elseif(in_array($data->tran_type, ['Sold', 'Deprication']))
                                            <td></td>
                                            <td>{{ $data->at_amount }}</td>
                                            <td>{{ $balance }}</td>
                                            @php
                                                $balance = $balance + $data->at_amount;
                                            @endphp
                                            @else
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            @endif
                                            <td>
                                              <a href="{{ route('admin.expense.voucher', ['id' => $data->id]) }}" target="_blank" class="btn btn-info btn-xs" title="Voucher">
                                                  <i class="fa fa-info-circle" aria-hidden="true"></i> Voucher
                                              </a>
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
    </div>
</section>



@endsection

@section('script')

<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print"]
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
