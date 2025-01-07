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
                        <form action="{{route('payableLedger.Search')}}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">
                                        
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
                                            <label for="vendor_id">Vendor</label>
                                            <select name="vendor_id" id="vendor_id" class="form-control select2">
                                              {{-- <option value="">Select</option>
                                              @foreach ($vendors as $vendor)
                                              <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                              @endforeach --}}
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
            <div class="col-md-12">
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
                        
                            <h4>Payable Ledger</h4>
                        </div>
                        @php
                            $balance = 0;
                            $totalqty = 0;
                        @endphp

                        <div class="table-responsive">
                            <table id="dataTransactionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Client</th>
                                        <th>Mother Vessel</th>
                                        <th>Truck Number</th>
                                        <th>Challan No</th>
                                        <th>From-To</th>
                                        <th>Qty</th>
                                        <th>Debit</th>
                                        <th>Credit</th>
                                        <th>Payable Amount</th>                                
                                    </tr>
                                </thead>
                                <tbody>
                                    

                                    @foreach($data as $index => $data)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                            <td>{{ $data->client->name ?? 'N/A' }} </td>
                                            <td>{{ $data->motherVassel->name ?? 'N/A'  }}</td>
                                            <td>{{ $data->truck_number }}</td>
                                            <td>{{ $data->challan_no }}</td>
                                            <td>{{ $data->ghat->name ?? " " }}-{{$data->destination->name ?? " " }}</td>
                                            <td>{{ $data->dest_qty }}</td>
                                            <td>{{ $data->advance }}</td>
                                            <td>{{ $data->carrying_bill }}</td>
                                            <td>{{ number_format($data->carrying_bill - $data->advance, 2)}}</td>
                                            @php
                                                $balance = $balance + $data->carrying_bill - $data->advance;
                                                $totalqty = $totalqty + $data->dest_qty;
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4"></td>
                                        <td></td>
                                        <td>Total Qty</td>
                                        <td>{{ $totalqty }}</td>
                                        <td></td>
                                        <td>Total Payable: </td>
                                        <td>{{ number_format($balance, 2)}}</td>
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

<script>
    $(document).ready(function () {

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

    });

</script>
<script>
    $(document).ready(function() {
        $('#mv_id').change(function() {
            var mvId = $(this).val();
            $('#vendor_id').empty();
            console.log(mvId);
            if (mvId) {
                $.ajax({
                    url: "{{URL::to('/admin/get-vendors-list')}}"+"/"+mvId,
                    type: 'GET',
                    success: function(data) {
                        // console.log(data);
                        $('#vendor_id').append('<option value="">Select Vendor</option>');
                        $.each(data.vendors, function(key, value) {
                            $('#vendor_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#vendor_id').empty();
                $('#vendor_id').append('<option value="">Select Vendor</option>');
            }
        });
    });
</script>
@endsection
