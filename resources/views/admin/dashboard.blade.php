@extends('admin.layouts.admin')

@section('content')

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </div>
    </div>
  </div>
</div>
  <section class="content ">
    <div class="container-fluid">
      @php
        $vendor = \App\Models\Vendor::count();
        $runningMotherVessel = \App\Models\MotherVassel::where('status', '1')->count();
        $completedMotherVessel = \App\Models\MotherVassel::where('status', '2')->count();
      @endphp
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>{{ $runningMotherVessel }}</h3>
              <p>Running Mother Vessels</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>{{ $completedMotherVessel }}</h3>
              <p>Completed Mother Vessels</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3> {{ $vendor }}</h3>
              <p>Vendors</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <a href="{{ route('challanPostingVendorReport') }}">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>Challan Posting</h3>
              <p>Check Challan Posting</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-6">
          <a href="{{ route('vendorLedger') }}">
          <div class="small-box bg-info">
            <div class="inner">
              <h3>Vendor Ledger</h3>
              <p>Check Vendor Ledger</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-6">
          <a href="{{ route('admin.addProgram') }}">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>Before Challan</h3>
              <p>Check Before Challan Posting</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-6">
          <a href="{{ route('admin.afterPostProgram') }}">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>After Challan</h3>
              <p>Check After Challan Posting</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            </div>
          </a>
        </div>
        <div class="col-lg-3 col-6">
          <a href="{{ route('admin.allProgram') }}">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>All Proragms</h3>
              <p>Check All Programs</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            </div>
          </a>
        </div>
        <!-- ./col -->
      </div>

    </div>
  </section>

  @if(in_array('1', json_decode(auth()->user()->role->permission)))
  <section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <section class="col-lg-7">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card card-secondary">
                  <div class="card-header">
                    <h3 class="card-title">All Data</h3>
                  </div>
                  <div class="card-body">                
                    <table id="example1" class="table table-bordered table-striped">
                      <thead class="bg-secondary">
                          <tr>
                              <th style="text-align: center">ID</th>
                              <th style="text-align: center">Date</th>
                              <th style="text-align: center">Description</th>
                              <th style="text-align: center">Payment Type</th>
                              <th style="text-align: center">Amount</th>
                              {{-- <th style="text-align: center">Total Amount</th> --}}
                          </tr>
                      </thead>
                      <tbody>
                        @php
                            $tran = \App\Models\Transaction::where(function ($query) {
                                      $query->where('tran_type', 'Advance')
                                      ->where('date', \Carbon\Carbon::today()->format('Y-m-d'));
                                  })
                                  ->orWhere(function ($query) {
                                      $query->where('table_type', 'Expenses')
                                      ->where('date', \Carbon\Carbon::today()->format('Y-m-d'));
                                  })->get();

                            $total = 0;
                        @endphp
                          @foreach ($tran as $key => $data)
                          <tr>
                              <td style="text-align: center">{{$data->tran_id ?? " " }}</td>
                              <td style="text-align: center">{{$data->date ?? " " }}</td>
                              <td style="text-align: center">{{$data->tran_type}}
                                <br> {{$data->chartOfAccount->account_name ?? " "}}
                                <br> <small>{{$data->description ?? " "}}</small>
                              </td>
                              <td style="text-align: center">{{$data->payment_type}}</td>
                              <td style="text-align: center">{{$data->amount}}</td>
                              {{-- <td style="text-align: center">{{$data->amount}}</td> --}}
                          </tr>
                          @php
                              $total += $data->amount;
                          @endphp
                          @endforeach
                      </tbody>
                      <tfoot>
                        <tr>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td style="text-align: right">Total</td>
                          <td style="text-align: center">{{$total}}</td>
                        </tr>
                      </tfoot>
                  </table>

                  </div>
                  <!-- /.card-body -->
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </section>
        <section class="col-lg-5 connectedSortable">
        </section>
      </div>
    </div>
  </section>
  @endif


@endsection

@section('script')

<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"],
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  });
</script>

@endsection
