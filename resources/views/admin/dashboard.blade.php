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
  <section class="content d-none">
    <div class="container-fluid">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-info">
            <div class="inner">
              <h3>150</h3>

              <p>New Orders</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">
              <h3>53<sup style="font-size: 20px">%</sup></h3>

              <p>Bounce Rate</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>44</h3>

              <p>User Registrations</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>65</h3>

              <p>Unique Visitors</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <!-- ./col -->
      </div>
      <!-- /.row -->
      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
          

          <!-- TO DO List -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="ion ion-clipboard mr-1"></i>
                To Do List
              </h3>

              <div class="card-tools">
                <ul class="pagination pagination-sm">
                  <li class="page-item"><a href="#" class="page-link">&laquo;</a></li>
                  <li class="page-item"><a href="#" class="page-link">1</a></li>
                  <li class="page-item"><a href="#" class="page-link">2</a></li>
                  <li class="page-item"><a href="#" class="page-link">3</a></li>
                  <li class="page-item"><a href="#" class="page-link">&raquo;</a></li>
                </ul>
              </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <ul class="todo-list" data-widget="todo-list">
                <li>
                  <!-- drag handle -->
                  <span class="handle">
                    <i class="fas fa-ellipsis-v"></i>
                    <i class="fas fa-ellipsis-v"></i>
                  </span>
                  <!-- checkbox -->
                  <div  class="icheck-primary d-inline ml-2">
                    <input type="checkbox" value="" name="todo1" id="todoCheck1">
                    <label for="todoCheck1"></label>
                  </div>
                  <!-- todo text -->
                  <span class="text">Design a nice theme</span>
                  <!-- Emphasis label -->
                  <small class="badge badge-danger"><i class="far fa-clock"></i> 2 mins</small>
                  <!-- General tools such as edit or delete-->
                  <div class="tools">
                    <i class="fas fa-edit"></i>
                    <i class="fas fa-trash-o"></i>
                  </div>
                </li>
                <li>
                  <span class="handle">
                    <i class="fas fa-ellipsis-v"></i>
                    <i class="fas fa-ellipsis-v"></i>
                  </span>
                  <div  class="icheck-primary d-inline ml-2">
                    <input type="checkbox" value="" name="todo2" id="todoCheck2" checked>
                    <label for="todoCheck2"></label>
                  </div>
                  <span class="text">Make the theme responsive</span>
                  <small class="badge badge-info"><i class="far fa-clock"></i> 4 hours</small>
                  <div class="tools">
                    <i class="fas fa-edit"></i>
                    <i class="fas fa-trash-o"></i>
                  </div>
                </li>
                <li>
                  <span class="handle">
                    <i class="fas fa-ellipsis-v"></i>
                    <i class="fas fa-ellipsis-v"></i>
                  </span>
                  <div  class="icheck-primary d-inline ml-2">
                    <input type="checkbox" value="" name="todo3" id="todoCheck3">
                    <label for="todoCheck3"></label>
                  </div>
                  <span class="text">Let theme shine like a star</span>
                  <small class="badge badge-warning"><i class="far fa-clock"></i> 1 day</small>
                  <div class="tools">
                    <i class="fas fa-edit"></i>
                    <i class="fas fa-trash-o"></i>
                  </div>
                </li>
                <li>
                  <span class="handle">
                    <i class="fas fa-ellipsis-v"></i>
                    <i class="fas fa-ellipsis-v"></i>
                  </span>
                  <div  class="icheck-primary d-inline ml-2">
                    <input type="checkbox" value="" name="todo4" id="todoCheck4">
                    <label for="todoCheck4"></label>
                  </div>
                  <span class="text">Let theme shine like a star</span>
                  <small class="badge badge-success"><i class="far fa-clock"></i> 3 days</small>
                  <div class="tools">
                    <i class="fas fa-edit"></i>
                    <i class="fas fa-trash-o"></i>
                  </div>
                </li>
                <li>
                  <span class="handle">
                    <i class="fas fa-ellipsis-v"></i>
                    <i class="fas fa-ellipsis-v"></i>
                  </span>
                  <div  class="icheck-primary d-inline ml-2">
                    <input type="checkbox" value="" name="todo5" id="todoCheck5">
                    <label for="todoCheck5"></label>
                  </div>
                  <span class="text">Check your messages and notifications</span>
                  <small class="badge badge-primary"><i class="far fa-clock"></i> 1 week</small>
                  <div class="tools">
                    <i class="fas fa-edit"></i>
                    <i class="fas fa-trash-o"></i>
                  </div>
                </li>
                <li>
                  <span class="handle">
                    <i class="fas fa-ellipsis-v"></i>
                    <i class="fas fa-ellipsis-v"></i>
                  </span>
                  <div  class="icheck-primary d-inline ml-2">
                    <input type="checkbox" value="" name="todo6" id="todoCheck6">
                    <label for="todoCheck6"></label>
                  </div>
                  <span class="text">Let theme shine like a star</span>
                  <small class="badge badge-secondary"><i class="far fa-clock"></i> 1 month</small>
                  <div class="tools">
                    <i class="fas fa-edit"></i>
                    <i class="fas fa-trash-o"></i>
                  </div>
                </li>
              </ul>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
              <button type="button" class="btn btn-primary float-right"><i class="fas fa-plus"></i> Add item</button>
            </div>
          </div>
          <!-- /.card -->
        </section>
        <!-- /.Left col -->
        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable">


          
          <!-- /.card -->
        </section>
        <!-- right col -->
      </div>
      <!-- /.row (main row) -->
    </div><!-- /.container-fluid -->
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
