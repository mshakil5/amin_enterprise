@extends('admin.layouts.admin')

@section('content')



<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Vendor Ledger</h3>
                    </div>
                    
                    
                    <div class="card-body">
                        <div class="ermsg"> </div>
                        <form action="{{route('vendorVasselLedger')}}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="client_id">Client</label>
                                            <select name="client_id" id="client_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($clients as $client)
                                              <option value="{{$client->id}}">{{$client->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label for="mv_id">Mother Vassel </label>
                                            <select name="mv_id" id="mv_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Action</label> <br>
                                            <button type="submit" class="btn btn-secondary">Check</button>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            
                        </form>
                    </div>
                    <div class="card-footer"> </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        {{-- <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div> --}}
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <h4> Ledger</h4>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        <div class="text-center mb-4 company-name-container">
                            @php
                            $company = \App\Models\CompanyDetail::select('company_name')->first();
                            @endphp
                            <h2>{{ $company->company_name }}</h2>
                        
                            <h4>Receivable Ledger</h4>
                        </div>

                        <div class="table-responsive">
                            <table id="dataTransactionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>Mother Vessel</th>
                                        <th>Payment Type</th>
                                        <th>Transaction Type</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Balance</th>                                
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $balance = $drAmount - $crAmount;
                                    @endphp

                                    @foreach($data as $index => $data)
                                        <tr>
                                            <td>{{ $data->tran_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                            <td>{{ $data->client->name ?? 'N/A' }} </td>
                                            <td>{{ $data->motherVassel->name ?? 'N/A'  }}</td>
                                            <td>{{ $data->payment_type }}</td>
                                            <td>{{ $data->tran_type }}</td>
                                            @if(in_array($data->tran_type, ['Received']))
                                            <td>{{ $data->amount }}</td>
                                            <td></td>
                                            <td>{{ $balance }}</td>
                                            @php
                                                $balance = $balance - $data->amount;
                                            @endphp
                                            @elseif(in_array($data->tran_type, ['Advance']))
                                            <td></td>
                                            <td>{{ $data->amount }}</td>
                                            <td>{{ $balance }}</td>
                                            @php
                                                $balance = $balance + $data->amount;
                                            @endphp
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
    </div>
</section>



@endsection

@section('script')

<script>
    $(function () {

    //   $("#dataTransactionsTable").DataTable({
    //     "responsive": true, "lengthChange": false, "autoWidth": false,
    //     "buttons": ["copy", "csv", "excel", "pdf", "print"],
    //     "lengthMenu": [[100, "All", 50, 25], [100, "All", 50, 25]],
    //   }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        $("#dataTransactionsTable").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            lengthMenu: [[100, 50, 25, -1], [100, 50, 25, "All"]],
            order: [[0, 'desc']], // Order by the first column (index 0) in descending order
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
    });
</script>
@endsection
