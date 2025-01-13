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
                <h3 class="card-title">Add new petrol pump</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="ermsg"></div>
                <form id="createThisForm">
                  @csrf
                  <input type="hidden" class="form-control" id="codeid" name="codeid">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Name*</label>
                        <input type="text" class="form-control" id="name" name="name">
                      </div>
                    </div>

                    <div class="col-sm-12">
                      <div class="form-group">
                        <label>Location</label>
                        <input type="text" class="form-control" id="location" name="location">
                      </div>
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
                <thead>
                <tr>
                  <th>Sl</th>
                  <th style="text-align: center">Name</th>
                  <th style="text-align: center">Location</th>
                  <th style="text-align: center">Pump Invoice</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{$data->name}}</td>
                    <td style="text-align: center">{{$data->location}}</td>
                    
                    <td style="text-align: center">
                      
                      <span class="btn btn-success btn-xs add-sq-btn" style="cursor: pointer;" data-id="{{ $data->id }}">+ add</span>

                      <span class="btn btn-info btn-xs view-btn" style="cursor: pointer;" data-id="{{ $data->id }}">View</span>

                    </td>

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


<div class="modal fade" id="payModal" tabindex="-1" role="dialog" aria-labelledby="payModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="payModalLabel">Add sequence form</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form id="payForm">
              <div class="modal-body">
                <div class="permsg"></div>
                
                  <div class="form-group">
                    <label for="date">Date<span style="color: red;">*</span></label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                  
                  </div>
                  <div class="form-group">
                      <label for="bill_number">Fuel Bill Number <span style="color: red;">*</span></label>
                      <input type="text" class="form-control" id="bill_number" name="bill_number" >
                  </div>
                  <div class="form-group">
                      <label for="invqty">Invoice qty <span style="color: red;">*</span></label>
                      <input type="number" class="form-control" id="invqty" name="invqty" >
                  </div>
                  
                  <div class="form-group">
                    <label for="vehicle_count">Total Vehicle<span style="color: red;">*</span></label>
                    <input type="number" class="form-control" id="vehicle_count" name="vehicle_count" >
                </div>

                  

              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-warning">Create</button>
              </div>
          </form>
      </div>
  </div>
</div>



<div class="modal fade" id="tranModal" tabindex="-1" role="dialog" aria-labelledby="tranModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="tranModalLabel">Sequence number and quantity</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>

          <div class="modal-body">
            <table id="trantable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Challan</th>
                  <th>Challan Store</th>
                  <th>Sequence</th>
                  <th>Unique ID</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
          </div>
      </div>
  </div>
</div>



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
      var url = "{{URL::to('/admin/pump')}}";
      var upurl = "{{URL::to('/admin/pump-update')}}";
      // console.log(url);
      $("#addBtn").click(function(){
      //   alert("#addBtn");
          if($(this).val() == 'Create') {
              var form_data = new FormData();
              form_data.append("name", $("#name").val());
              form_data.append("location", $("#location").val());
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
              form_data.append("name", $("#name").val());
              form_data.append("location", $("#location").val());
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
          $("#name").val(data.name);
          $("#location").val(data.location);
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


      $("#contentContainer").on('click', '.add-sq-btn', function () {
          var id = $(this).data('id');
          $('#payModal').modal('show');
          $('#payForm').off('submit').on('submit', function (event) {
              event.preventDefault();

              var form_data = new FormData();
              form_data.append("pumpId", id);
              form_data.append("date", $("#date").val());
              form_data.append("bill_number", $("#bill_number").val());
              form_data.append("invqty", $("#invqty").val());
              form_data.append("vehicle_count", $("#vehicle_count").val());

              if (!$("#bill_number").val()) {
                  alert('Please enter bill number.');
                  return;
              }

              if (!$("#invqty").val()) {
                  alert('Please enter quantity.');
                  return;
              }

              if (!$("#vehicle_count").val()) {
                  alert('Please enter total vehicle.');
                  return;
              }

              $.ajax({
                  url: '{{ URL::to('/admin/add-fuel-bill-number') }}',
                  method: 'POST',
                  data:form_data,
                  contentType: false,
                  processData: false,
                  // dataType: 'json',
                  success: function (response) {
                    if (response.status == 303) {
                        $(".permsg").html(response.message);
                    }else if(response.status == 300){

                      $(".permsg").html(response.message);
                      window.setTimeout(function(){location.reload()},2000)
                    }
                    
                      console.log(response);
                      $('#payModal').modal('hide');

                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
          });
      });

      $('#payModal').on('hidden.bs.modal', function () {
            $('#paymentAmount').val('');
            $('#paymentNote').val('');
      });


      $("#contentContainer").on('click', '.view-btn', function () {
          var id = $(this).data('id');
          $('#tranModal').modal('show');
              // console.log(id);
              var form_data = new FormData();
              form_data.append("vendorId", id);

              $.ajax({
                  url: '{{ URL::to('/admin/get-vendor-sequence') }}',
                  method: 'POST',
                  data:form_data,
                  contentType: false,
                  processData: false,
                  // dataType: 'json',
                  success: function (response) {
                    // console.log(response);
                      $('#trantable tbody').html(response.data);
                  },
                  error: function (xhr) {
                      console.log(xhr.responseText);
                  }
              });
      });
  });
</script>
@endsection