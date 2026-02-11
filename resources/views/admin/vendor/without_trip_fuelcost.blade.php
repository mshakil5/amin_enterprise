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



<section class="content" id="addThisFormContainer">
  <div class="container-fluid">
    <div class="row justify-content-md-center">
      <div class="col-md-10">
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Add New Without Trip Fuel Bill</h3>
          </div>
          <div class="card-body">
            <div class="ermsg"></div>
            <form id="createThisForm">
              @csrf
              <input type="hidden" id="codeid" name="codeid">
              <input type="hidden" id="vendorid" name="vendorid" value="{{$vendor->id}}">
              
              <div class="row">
                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="sequence_id">Vendor Sequence</label>
                    <select name="sequence_id" id="sequence_id" class="form-control select2" style="width: 100%;">
                      <option value="">Select Sequence</option>
                      @foreach ($sequences as $sequence)
                        <option value="{{ $sequence->id }}">{{ $sequence->unique_id }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="fuel_bill_id">Fuel Bill ID</label>
                    <select name="fuel_bill_id" id="fuel_bill_id" class="form-control select2" style="width: 100%;">
                      <option value="">Select Fuel Bill</option>
                      @foreach ($fuelBills as $fuelBill)
                        <option value="{{ $fuelBill->id }}">{{ $fuelBill->unique_id }} - ({{ $fuelBill->bill_number }})</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="amount">Amount</label>
                    <div class="input-group">
                      <input type="number" class="form-control" id="amount" name="amount" placeholder="0.00">
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}">
                  </div>
                </div>

                <div class="col-sm-12">
                  <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="2" placeholder="Enter details..."></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <div class="card-footer">
            <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create</button>
            <button type="button" id="FormCloseBtn" class="btn btn-default">Cancel</button>
          </div>
        </div>
      </div>
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
              <h3 class="card-title">All Data</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Tran ID</th>
                  <th>Vendor Sequence</th>
                  <th>Fuel Bill ID</th>
                  <th>Amount</th>
                  <th>Description</th>
                </tr>
                </thead>
                <tbody>

                  @foreach ($data as $key => $data)

                  <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $data->tran_id }}</td>
                    <td>{{ $data->vendorSequenceNumber->unique_id }}</td>
                    <td>{{ $data->fuelBill->unique_id }} ({{ $data->fuelBill->bill_number }})</td>
                    <td>{{ $data->amount }}</td>
                    <td>{{ $data->description }}</td>
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

    });
  </script>

<script>
    $(document).ready(function () {
        $("#addThisFormContainer").hide();
        
        // CSRF Token Setup
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        const storeUrl = "{{ route('admin.withouttrip.fuelbill.store') }}";
        const updateUrl = "{{ route('admin.withouttrip.fuelbill.update') }}";

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

        // Submit Logic (Create & Update)
        $("#addBtn").click(function(e){
            e.preventDefault();
            
            let form_data = new FormData();
            form_data.append("date", $("#date").val());
            form_data.append("vendorid", $("#vendorid").val());
            form_data.append("sequence_id", $("#sequence_id").val());
            form_data.append("fuel_bill_id", $("#fuel_bill_id").val());
            form_data.append("amount", $("#amount").val());
            form_data.append("description", $("#description").val());
            form_data.append("codeid", $("#codeid").val());

            let targetUrl = ($(this).val() === 'Update') ? updateUrl : storeUrl;

            $.ajax({
                url: targetUrl,
                method: "POST",
                contentType: false,
                processData: false,
                data: form_data,
                success: function (d) {
                    if (d.status == 303) {
                        $(".ermsg").html('<div class="alert alert-danger">'+d.message+'</div>');
                    } else if(d.status == 300) {
                        $(".ermsg").html('<div class="alert alert-success">'+d.message+'</div>');
                        window.setTimeout(function(){ location.reload() }, 1500);
                    }
                },
                error: function (d) { console.log(d); }
            });
        });

        // Edit Functionality
        function clearform(){
            $('#createThisForm')[0].reset();
            // 3. Reset Select2 values visually
            $('.select2').val(null).trigger('change');
            $("#addBtn").val('Create').html('Create');
        }

        // 4. If you use Edit, make sure to trigger change for Select2
        window.populateForm = function(data) {
            $("#codeid").val(data.id);
            $("#amount").val(data.amount);
            $("#date").val(data.date);
            $("#description").val(data.description);
            
            // Trigger Select2 to show correct values
            $("#sequence_id").val(data.sequence_id).trigger('change');
            $("#fuel_bill_id").val(data.fuel_bill_id).trigger('change');

            $("#addBtn").val('Update').html('Update');
            $("#addThisFormContainer").show(300, function() {
                initSelect2();
            });
            $("#newBtn").hide(100);
        };
    });
</script>

@endsection