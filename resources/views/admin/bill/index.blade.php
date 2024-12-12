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
            {{-- <a href="{{route('admin.allProgram')}}" class="btn btn-secondary my-3">Back</a> --}}
        </div>
      </div>
    </div>
</section>
<!-- /.content -->


<section class="content pt-3" id="addThisFormContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-12">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title" id="cardTitle">Bill Received</h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="ermsg"> </div>
                        
                        <form id="createThisForm">
                            @csrf

                            <div class="row">
                                <div class="col-sm-12">
                                    
                                    <div class="form-row">

                                        <div class="form-group col-md-3">
                                            <label for="client_id">Client<span style="color: red;">*</span> </label>
                                            <select name="client_id" id="client_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($clients as $client)
                                              <option value="{{$client->id}}">{{$client->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col-md-4">
                                            <label for="mv_id">Mother Vassel<span style="color: red;">*</span> </label>
                                            <select name="mv_id" id="mv_id" class="form-control select2">
                                              <option value="">Select</option>
                                              @foreach ($mvassels as $mvassel)
                                              <option value="{{$mvassel->id}}">{{$mvassel->name}}</option>
                                              @endforeach
                                            </select>
                                        </div>

                                        
                                        <div class="form-group col-md-2">
                                            <label>Action</label> <br>
                                            <button type="button" form="createThisForm" id="checkBtn"  class="btn btn-secondary">Check</button>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>
                            
                        </form>
                    </div>
                    <div class="card-footer"> </div>
                </div>
            </div>

            <div class="mt-3"></div>

        </div>
    </div>
</section>

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">All Generated Challan</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Bill Status</th>
                  <th>Bill No</th>
                  <th>Date</th>
                  <th>Vendor</th>
                  <th>Header ID</th>
                  <th>Truck Number</th>
                  <th>Challan no</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">

                      <label class="form-checkbox  grid layout">
                        <input type="checkbox" name="checkbox-checked" class="custom-checkbox"  @if ($data->generate_bill == 1) checked @endif  />
                      </label>

                    </td>
                    <td style="text-align: center">{{$data->bill_no}}</td>
                    <td style="text-align: center">{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y')}}</td>
                    <td style="text-align: center">{{$data->vendor->name}}</td>
                    <td style="text-align: center">{{$data->headerid}}</td>
                    <td style="text-align: center">{{$data->truck_number}}</td>
                    <td style="text-align: center">{{$data->challan_no}}</td>


                    <td style="text-align: center">
                        <label class="form-checkbox  grid layout">
                            <input type="checkbox" name="checkbox-checked" class="custom-checkbox"  @if ($data->bill_status == 1) checked @endif  />
                          </label>
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


<!--  Program after challan data store start -->
<script>
    $(document).ready(function() {
        $(document).on('click', '#afterChallanBtn', function(e) {
            e.preventDefault();

            // $(this).attr('disabled', true);
            // $('#loader').show();
            var prgmdtlid = $('#prgmdtlid').val();


            // console.log(prgmdtlid, vendorid, truck_number, fuelqty, fuel_rate, fuel_amount, tamount, fueltoken, tamount,);
            var formData = new FormData($('#addadvThisForm')[0]);
            formData.append("vendor_id", $('#vendor_id'+prgmdtlid).val());
            formData.append("truck_number", $('#truck_number'+prgmdtlid).val());
            formData.append("fuelqty", $('#fuelqty'+prgmdtlid).val());
            formData.append("fuel_rate", $('#fuel_rate'+prgmdtlid).val());
            formData.append("fuel_amount", $('#fuel_amount'+prgmdtlid).val());
            formData.append("amount", $('#amount'+prgmdtlid).val());
            formData.append("fueltoken", $('#fueltoken'+prgmdtlid).val());

            $.ajax({
                url: '{{ route("after-challan-store") }}',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {

                    if (status == 400) {
                        $(".ermsg").html(response.message);
                    } else {
                        console.log(response);
                        $(".ermsg").html(response.message);
                        // window.setTimeout(function(){location.reload()},2000)
                    }

                    
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseJSON.message);
                    // console.error(xhr.responseText);
                },
                complete: function() {
                    $('#loader').hide();
                    $('#addBtn').attr('disabled', false);
                }
            });
        });

    });
</script>
<!-- Program after challan data store End -->



@endsection