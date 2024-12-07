@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content mt-3" id="newBtnSection">
    <div class="container-fluid">
      <div class="row">
        <div class="col-2">
            <button type="button" class="btn btn-secondary my-3" id="newBtn">Add new</button>
        </div>
      </div>
    </div>
</section>
  <!-- /.content -->



    <!-- Main content -->
    <section class="content" id="addThisFormContainer">
      <div class="container-fluid">
        <div class="row justify-content-md-center">
          <!-- right column -->
          <div class="col-md-8">
            <!-- general form elements disabled -->
            <div class="card card-secondary">
              <div class="card-header">
                <h3 class="card-title">Add new destination slab rate</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="ermsg"></div>
                <form id="createThisForm">
                  @csrf
                  <input type="hidden" class="form-control" id="codeid" name="codeid">
                  <div class="dermsg"></div>
                      <div class="form-row">

                        <div class="form-group col-md-4">
                            <label for="client_id">Client </label>
                            <select name="client_id" id="client_id" class="form-control">
                              <option value="">Select</option>
                              @foreach (\App\Models\Client::where('status', 1)->get() as $client)
                              <option value="{{$client->id}}">{{$client->name}}</option>
                              @endforeach
                            </select>
                        </div>
                          <div class="form-group col-md-4">
                              <label for="ghat_id">Ghat </label>
                              <select name="ghat_id" id="ghat_id" class="form-control">
                                <option value="">Select</option>
                                @foreach (\App\Models\Ghat::where('status', 1)->get() as $ghat)
                                <option value="{{$ghat->id}}">{{$ghat->name}}</option>
                                @endforeach
                              </select>
                          </div>
                          <div class="form-group col-md-4">
                              <label for="destination_id">Destination </label>
                              <select name="destination_id" id="destination_id" class="form-control">
                                <option value="">Select</option>
                                @foreach (\App\Models\Destination::where('status', 1)->get() as $dest)
                                <option value="{{$dest->id}}">{{$dest->name}}</option>
                                @endforeach
                              </select>
                          </div>
                          <div class="form-group col-md-3">
                              <label for="title">Title</label>
                              <input type="text" class="form-control" id="title" name="title" >
                          </div>
                          <div class="form-group col-md-3">
                              <label for="qty">Qty</label>
                              <input type="number" class="form-control" id="qty" name="qty" value="12" >
                          </div>
                          <div class="form-group col-md-3">
                              <label for="below_rate_per_qty">Below Rate</label>
                              <input type="number" class="form-control" id="below_rate_per_qty" name="below_rate_per_qty" >
                          </div>
                          <div class="form-group col-md-3">
                              <label for="above_rate_per_qty">Above Rate</label>
                              <input type="number" class="form-control" id="above_rate_per_qty" name="above_rate_per_qty" >
                          </div>
                          
                      </div>
                </form>
              </div>

              
              <!-- /.card-body -->
              <div class="card-footer">
                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
                <button type="submit" id="FormCloseBtn" class="btn btn-default">Cancel</button>
              </div>
              <!-- /.card-footer -->
              <!-- /.card-body -->
            </div>
          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
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
              <h3 class="card-title">All Data</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              
              <table id="example1" class="table table-bordered table-striped">
                <thead class="bg-secondary">
                    <tr>
                        <th style="text-align: center">Client</th>
                        <th style="text-align: center">Ghat</th>
                        <th style="text-align: center">Destination</th>
                        <th style="text-align: center">Qty</th>
                        <th style="text-align: center">Below rate</th>
                        <th style="text-align: center">Above rate</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (\App\Models\ClientRate::get() as $key => $data)
                    <tr>
                        <td style="text-align: center">{{$data->client->name}}</td>
                        <td style="text-align: center">{{$data->ghat->name}}</td>
                        <td style="text-align: center">{{$data->destination->name}}</td>
                        <td style="text-align: center">{{$data->maxqty}}</td>
                        <td style="text-align: center">{{$data->below_rate_per_qty}}</td>
                        <td style="text-align: center">{{$data->above_rate_per_qty}}</td>
                        <td style="text-align: center">
                          <a id="EditBtn" rid="{{$data->id}}"><i class="fa fa-edit" style="color: #2196f3;font-size:16px;"></i></a>
                          <a id="deleteBtn" rid="{{$data->id}}"><i class="fa fa-trash-o" style="color: red;font-size:16px;"></i></a>
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
      $("#addThisFormContainer").hide();
      $("#newBtn").click(function(){
          clearform();
          $("#newBtn").hide(100);
          $("#addThisFormContainer").show(300);

      });
      $("#FormCloseBtn").click(function(){
          $("#addThisFormContainer").hide(200);
          $("#newBtn").show(100);
          clearform();
      });
      //header for csrf-token is must in laravel
      $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
      //
      var url = "{{URL::to('/admin/client-rate')}}";
      var upurl = "{{URL::to('/admin/client-rate-update')}}";
      // console.log(url);
      $("#addBtn").click(function(){
      //   alert("#addBtn");

    

          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("client_id", $("#client_id").val());
              form_data.append("destination_id", $("#destination_id").val());
              form_data.append("ghat_id", $("#ghat_id").val());
              form_data.append("qty", $("#qty").val());
              form_data.append("below_rate_per_qty", $("#below_rate_per_qty").val());
              form_data.append("above_rate_per_qty", $("#above_rate_per_qty").val());
              form_data.append("title", $("#title").val());
              $.ajax({
                url: url,
                method: "POST",
                contentType: false,
                processData: false,
                data:form_data,
                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html(d.message);
                    }else if(d.status == 300){

                      $(".ermsg").html(d.message);
                      window.setTimeout(function(){location.reload()},2000)
                    }
                },
                error: function (d) {
                    console.log(d);
                }
            });
          }
          //create  end
          //Update
          if($(this).val() == 'Update'){
              var form_data = new FormData();
              form_data.append("client_id", $("#client_id").val());
              form_data.append("destination_id", $("#destination_id").val());
              form_data.append("ghat_id", $("#ghat_id").val());
              form_data.append("qty", $("#qty").val());
              form_data.append("below_rate_per_qty", $("#below_rate_per_qty").val());
              form_data.append("above_rate_per_qty", $("#above_rate_per_qty").val());
              form_data.append("title", $("#title").val());
              form_data.append("codeid", $("#codeid").val());
              
              $.ajax({
                  url:upurl,
                  type: "POST",
                  dataType: 'json',
                  contentType: false,
                  processData: false,
                  data:form_data,
                  success: function(d){
                      console.log(d);
                      if (d.status == 303) {
                          $(".ermsg").html(d.message);
                          pagetop();
                      }else if(d.status == 300){
                        $(".ermsg").html(d.message);
                          window.setTimeout(function(){location.reload()},2000)
                      }
                  },
                  error:function(d){
                      console.log(d);
                  }
              });
          }
          //Update
      });
      //Edit
      $("#contentContainer").on('click','#EditBtn', function(){
          //alert("btn work");
          codeid = $(this).attr('rid');
          //console.log($codeid);
          info_url = url + '/'+codeid+'/edit';
          //console.log($info_url);
          $.get(info_url,{},function(d){
              populateForm(d);
              pagetop();
          });
      });
      //Edit  end
      //Delete 
      $("#contentContainer").on('click','#deleteBtn', function(){
            if(!confirm('Sure?')) return;
            codeid = $(this).attr('rid');
            info_url = url + '/'+codeid;
            $.ajax({
                url:info_url,
                method: "GET",
                type: "DELETE",
                data:{
                },
                success: function(d){
                    if(d.success) {
                        alert(d.message);
                        location.reload();
                    }
                },
                error:function(d){
                    console.log(d);
                }
            });
        });
      //Delete  

      function populateForm(data){
          $("#client_id").val(data.client_id);
          $("#destination_id").val(data.destination_id);
          $("#ghat_id").val(data.ghat_id);
          $("#maxqty").val(data.qty);
          $("#below_rate_per_qty").val(data.below_rate_per_qty);
          $("#above_rate_per_qty").val(data.above_rate_per_qty);
          $("#title").val(data.title);
          $("#codeid").val(data.id);
          $("#addBtn").val('Update');
          $("#addBtn").html('Update');
          $("#addThisFormContainer").show(300);
          $("#newBtn").hide(100);
      }
      function clearform(){
          $('#createThisForm')[0].reset();
          $("#addBtn").val('Create');
      }
  });
</script>
@endsection