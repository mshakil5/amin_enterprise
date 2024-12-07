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
              <h3 class="card-title">Vendor List</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sl</th>
                  <th>Vendor</th>
                  <th>Qty</th>
                  <th>Carring Bill</th>
                  <th>Line Charge</th>
                  <th>Scale fee</th>
                  <th>Other Cost</th>
                  <th>Advance</th>
                  <th>Due</th>
                  <th>Total Paid</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                  @foreach ($data as $key => $data)
                  @php
                      $totalPaid = \App\Models\Transaction::where('vendor_id', $data->vendor_id)->where('program_id', $pid)->sum('amount')
                  @endphp
                  <tr>
                    <td style="text-align: center">{{ $key + 1 }}</td>
                    <td style="text-align: center">{{$data->vendor->name}}-{{$data->vendor_id}}</td>
                    <td style="text-align: center">{{$data->total_dest_qty}}</td>
                    <td style="text-align: center">{{$data->total_carrying_bill}}</td>
                    <td style="text-align: center">{{$data->total_line_charge}}</td>
                    <td style="text-align: center">{{$data->total_scale_fee}}</td>
                    <td style="text-align: center">{{$data->total_other_cost}}</td>
                    <td style="text-align: center">{{$data->total_advance}}</td>
                    <td style="text-align: center">{{$data->total_due}}</td>
                    <td style="text-align: center">{{$totalPaid}}</td>
                    <td style="text-align: center">
                      <span class="badge badge-success payment-btn" style="cursor: pointer;" data-id="{{ $data->id }}" data-vendor-id="{{ $data->vendor_id }}" data-program-id="{{ $pid }}">Pay</span>

                      <span class="badge badge-secondary trn-btn" style="cursor: pointer;" data-id="{{ $data->id }}" data-vendor-id="{{ $data->vendor_id }}">Transaction</span>
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
              <h5 class="modal-title" id="payModalLabel">Vendor Payment Form</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form id="payForm">
              <div class="modal-body">
                  <div class="form-group">
                      <label for="paymentAmount">Payment Amount <span style="color: red;">*</span></label>
                      <input type="number" class="form-control" id="paymentAmount" name="paymentAmount" placeholder="Enter payment amount">
                  </div>

                  <div class="form-group">
                      <label for="payment_type">Payment Type <span style="color: red;">*</span></label>
                      <select name="payment_type" id="payment_type" class="form-control" >
                          <option value="Cash">Cash</option>
                          <option value="Bank">Bank</option>
                      </select>
                  </div>

                  <div class="form-group">
                      <label for="paymentNote">Payment Note</label>
                      <textarea class="form-control" id="paymentNote" name="paymentNote" rows="3" placeholder="Enter payment note"></textarea>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-warning">Pay</button>
              </div>
          </form>
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


    //
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    //



    $("#contentContainer").on('click', '.payment-btn', function () {
            var id = $(this).data('id');
            var vendorId = $(this).data('vendor-id');
            var programId = $(this).data('program-id');
            console.log(vendorId);
            $('#payModal').modal('show');
            $('#payForm').off('submit').on('submit', function (event) {
                event.preventDefault();

                var form_data = new FormData();
                form_data.append("id", id);
                form_data.append("vendorId", vendorId);
                form_data.append("programId", programId);
                form_data.append("paymentAmount", $("#paymentAmount").val());
                form_data.append("payment_type", $("#payment_type").val());
                form_data.append("paymentNote", $("#paymentNote").val());

                if (!$("#paymentAmount").val()) {
                    alert('Please enter a payment amount.');
                    return;
                }



                $.ajax({
                    url: '{{ URL::to('/admin/vendor-pay') }}',
                    method: 'POST',
                    data:form_data,
                    contentType: false,
                    processData: false,
                    // dataType: 'json',
                    success: function (response) {
                      console.log(response);
                        $('#payModal').modal('hide');
                        swal({
                            text: "Payment store successfully",
                            icon: "success",
                            button: {
                                text: "OK",
                                className: "swal-button--confirm"
                            }
                        }).then(() => {
                            location.reload();
                        });
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


  });
</script>

@endsection