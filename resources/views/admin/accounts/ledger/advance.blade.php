@extends('admin.layouts.admin')

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">



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
                        <form action="{{route('advanceLedger.Search')}}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        <div class="form-group col-md-2">
                                            <label for="client_id">Client</label>
                                            <select name="client_id" id="client_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($clients as $client)
                                              <option value="{{$client->id}}" @if ($client->id == $client_id) selected @endif>{{$client->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="form-group col-md-3">
                                            <label for="mv_id">Mother Vassel </label>
                                            <select name="mv_id" id="mv_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}" @if ($mvassel->id == $mvid) selected @endif>{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label for="vendor_id">Vendor</label>
                                            <select name="vendor_id" id="vendor_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($vendors as $vendor)
                                              <option value="{{$vendor->id}}" @if ($vendor->id == $vendor_id) selected @endif>{{$vendor->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label for="payment_type">Fuel/Cash</label>
                                            <select name="payment_type" id="payment_type" class="form-control select2">
                                              <option value="">Select</option>
                                              <option value="Fuel" @if ($payment_type == "Fuel") selected @endif>Fuel</option>
                                              <option value="Cash" @if ($payment_type == "Cash") selected @endif>Cash</option>
                                              
                                            </select>
                                        </div>

                                        <div class="form-group col-md-2">
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
                            Ledger 
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        <div class="text-center mb-4 company-name-container">
                            @php
                            $company = \App\Models\CompanyDetail::select('company_name')->first();
                            @endphp
                            <h2>{{ $company->company_name }}</h2>
                        
                            <h4>Advance Ledger</h4>
                        </div>
                        @php
                            $balance = $crAmount;
                        @endphp

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
                                        {{-- <th>Balance</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    

                                    @foreach($data as $index => $data)
                                        <tr>
                                            <td>{{ $data->tran_id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                            <td>{{ $data->client->name ?? 'N/A' }} </td>
                                            <td>{{ $data->motherVassel->name ?? 'N/A'  }}</td>
                                            <td>{{ $data->payment_type }}</td>
                                            <td>{{ $data->tran_type }}</td>
                                            @if(in_array($data->tran_type, ['Received']))
                                            
                                            @elseif(in_array($data->tran_type, ['Advance']))
                                            <td>{{ $data->amount }}</td>
                                            <td></td>
                                            {{-- <td>{{ number_format($balance, 0) }}</td> --}}
                                            @php
                                                $balance = $balance - $data->amount;
                                            @endphp
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>Total</th>
                                        <th>{{$crAmount}}</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
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

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.5/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


<script>
    $(document).ready(function () {
        $("#dataTransactionsTable").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            order: [[0, 'desc']], // Order by first column in descending order,
            lengthMenu: [[100, 50, 25, -1], [100, 50, 25, "All"]],
        }).buttons().container().appendTo('#dataTransactionsTable_wrapper .col-md-6:eq(0)');
    });

</script>
@endsection
