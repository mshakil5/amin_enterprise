@extends('admin.layouts.admin')

@section('content')

<style>
  .form-checkbox {
      font-family: system-ui, sans-serif;
      font-size: 2rem;
      font-weight: bold;
      line-height: 1.1;
      display: grid;
      grid-template-columns: 1em auto;
      gap: 0.5em;
    }

    .custom-checkbox {
      height: 30px;
    }
</style>
<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
  <div class="container-fluid">
    <div class="row">
      <div class="col-2">
          <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">Back</a>
          @if ($data->bill_status == 1)
          <a href="{{route('generatingBillShow', $data->id)}}" class="btn btn-secondary my-3">Bill Show </a>
          @else
          <a href="{{route('billGenerating', $data->id)}}" class="btn btn-secondary my-3 ">Generate Bill</a>
          @endif
          
      </div>
    </div>
  </div>
</section>
<!-- /.content -->

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Mother Vassel: {{$data->motherVassel->name}}</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Bill Status</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Header ID</th>
                  <th>Truck Number</th>
                  <th>Challan no</th>
                  <th>Destination</th>
                  <th>Qty</th>
                  <th>Carring Bill</th>
                  <th>Line Charge</th>
                  <th>Scale fee</th>
                  <th>Other Cost</th>
                  <th>Advance</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data->programDetail as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">

                      <label class="form-checkbox  grid layout">
                        <input type="checkbox" name="checkbox-checked" class="custom-checkbox"  @if ($data->generate_bill == 1) checked @endif  />
                      </label>

                    </td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                    <td style="text-align: center">{{$data->vendor->name}}</td>
                    <td style="text-align: center">{{$data->headerid}}</td>
                    <td style="text-align: center">{{$data->truck_number}}</td>
                    <td style="text-align: center">{{$data->challan_no}}</td>
                    <td style="text-align: center">{{$data->destination->name ?? ' '}}</td>
                    <td style="text-align: center">{{$data->dest_qty}}</td>
                    <td style="text-align: center">{{$data->carrying_bill}}</td>
                    <td style="text-align: center">{{$data->line_charge}}</td>
                    <td style="text-align: center">{{$data->scale_fee}}</td>
                    <td style="text-align: center">{{$data->other_cost}}</td>
                    <td style="text-align: center">{{$data->advance}}</td>

                    {{-- <td style="text-align: center">
                      <span class="badge badge-success adv-btn" style="cursor: pointer;" data-id="{{ $data->id }}" data-vendor-id="{{ $data->vendor_id }}" data-program-id="{{ $data->program_id }}">Advance Pay</span>

                      <span class="badge badge-secondary trn-btn" style="cursor: pointer;" data-id="{{ $data->id }}" data-vendor-id="{{ $data->vendor_id }}">Transaction</span>

                      @if ($data->programDestination)
                        <a class="btn btn-app destUpBtn" id="destinationUpBtn" rid="{{ $data->id }}" data-id="{{ $data->id }}" data-pdid="{{ $data->programDestination->id }}" data-vendor-id="{{ $data->vendor_id }}" data-program-id="{{ $data->program_id }}">
                          <i class="fa fa-map-marker" aria-hidden="true"></i> Destination
                        </a>
                      @else
                        <a class="btn btn-app destBtn" id="destinationBtn" rid="{{ $data->id }}" data-id="{{ $data->id }}" data-vendor-id="{{ $data->vendor_id }}" data-program-id="{{ $data->program_id }}">
                          <i class="fa fa-map-marker" aria-hidden="true"></i> Destination
                        </a>
                      @endif
                    </td> --}}

                    <td style="text-align: center">
                        <a class="btn btn-app" id="trnEditBtn" rid="{{ $data->id }}">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a class="btn btn-app" id="trndeleteBtn" rid="{{ $data->id }}">
                            <i class="fa fa-trash-o" style="color: red; font-size:16px;"></i>Delete
                        </a>
                    </td>
                  </tr>
                  @endforeach
                
                </tbody>
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
<!-- /.content -->


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





<script>
  $(document).ready(function () {


    //
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //



          


  });
</script>

@endsection