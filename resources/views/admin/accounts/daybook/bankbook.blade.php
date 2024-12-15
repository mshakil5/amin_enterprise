@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">Bank Book</h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        <div class="row">
                            <div class="row  justify-content-md-center mb-3">
                                <form class="form-inline" role="form" method="POST" action="{{route('admin.bankbookSearch')}}">
                                    {{ csrf_field() }}

                                    
                                    <div class="form-group col-md-2">
                                        <label for="vendor_id">Vendor</label>
                                        <select name="vendor_id" id="vendor_id" class="form-control select2">
                                          <option value="">Select</option>
                                          @foreach (\App\Models\Vendor::where('status', 1)->get() as $vendor)
                                          <option value="{{$vendor->id}}">{{$vendor->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="mv_id">Mother Vassel </label>
                                        <select name="mv_id" id="mv_id" class="form-control select2">
                                          <option value="">Select</option>
                                          @foreach (\App\Models\MotherVassel::where('status', 1)->get() as $mvassel)
                                          <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                          @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="form-group col-md-2">
                                        <label for="start_date">Start Date</label>
                                        <input type="date" class="form-control" name="start_date" value="{{ request()->input('start_date') }}">
                                    </div>
                                    
                                    <div class="form-group col-md-2">
                                        <label for="end_date">End Date</label>
                                        <input type="date" class="form-control" name="end_date" value="{{ request()->input('end_date') }}">
                                    </div>

                                    <button type="submit" class="btn btn-secondary">Search</button>
                                </form>
                            </div>
                            <div class="col-md-12">
                                <div class="text-center mb-4 company-name-container">
                                    
                                    <h4>Day Cash Book</h4>
                                </div>
                        
                                
                                <table id="daybookTable" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Sl</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Bill#</th>                            
                                            <th>Challan#</th>                            
                                            <th>Debit</th>                            
                                            <th>Credit</th>                            
                                            <th>Balance</th>                            
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php
                                            $balance = $totalAmount;
                                        @endphp

                                        @foreach($bankbooks as $key => $data)
                                            <tr>
                                                <td> {{ $key + 1 }} </td>
                                                <td>{{ \Carbon\Carbon::parse($data->date)->format('d-m-Y') }}</td>
                                                <td>
                                                    {{ $data->tran_type }} {{ $data->payment_type }} 
                                                </td>
                                                <td>{{ $data->bill_number }}</td>
                                                <td>{{ $data->challan_no }}</td>
                                                @if(in_array($data->tran_type, ['Current', 'Received']))
                                                <td>{{ number_format($data->amount, 2) }}</td>
                                                <td></td>
                                                <td>{{ number_format($balance, 2) }}</td>
                                                @php
                                                    $balance = $balance - $data->amount;
                                                @endphp
                                                @elseif(in_array($data->tran_type, ['Advance', 'Payment', 'Prepaid']))
                                                <td></td>
                                                <td>{{ number_format($data->amount, 2) }}</td>
                                                <td>{{ number_format($balance, 2) }}</td>
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
    </div>
</section>



@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#daybookTable').DataTable({
            pageLength: 100,
        });
    });
</script>

@endsection