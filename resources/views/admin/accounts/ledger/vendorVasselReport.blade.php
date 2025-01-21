@extends('admin.layouts.admin')

@section('content')

<!-- Main content -->
<section class="content" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <!-- /.card -->

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">Mother Vessel wise vendor ledger</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <h4 class="text-center">{{$vendors->name}}</h4>
              <h5 class="text-center">{{$mvassels->name}}</h5>
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Particular</th>
                  <th>Qty</th>
                  <th>Ltr</th>
                  <th>Trip</th>
                  <th>Dr.</th>
                  <th>Cr.</th>
                  <th>Balance</th>
                </tr>
                </thead>
                <tbody>
                    
                  <tr>
                    <td style="text-align: left">
                        Cash trip advance
                    </td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$cashAdv}}</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$cashAdv}}</td>
                  </tr>

                  <tr>
                    <td style="text-align: left">
                        Fuel Advance
                    </td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$fuelQty}}</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$fuelAdv}}</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$cashAdv + $fuelAdv}}</td>
                  </tr>

                  <tr>
                    <td style="text-align: left">
                        Carrying bill
                    </td>
                    <td style="text-align: center">{{$carryingQty}}</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$tripCount}}</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$carryingBill}}</td>
                    <td style="text-align: center">{{$cashAdv + $fuelAdv - $carryingBill}}</td>
                  </tr>

                  <tr>
                    <td style="text-align: left">
                        Scale cost payable to vendor <br> <small>(if applicable)</small>
                    </td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$scalecost}}</td>
                    <td style="text-align: center">{{$cashAdv + $fuelAdv - $carryingBill - $scalecost}}</td>
                  </tr>

                  <tr>
                    <td style="text-align: left">
                        Line charge deductable from vendor <br> <small>(if applicable)</small>
                    </td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$line_charge}}</td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$cashAdv + $fuelAdv + $line_charge - $carryingBill - $scalecost}}</td>
                  </tr>

                  <tr>
                    <td style="text-align: left">
                        Payment
                    </td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$cashAdv + $fuelAdv + $line_charge - $carryingBill - $scalecost}}</td>
                  </tr>

                  <tr>
                    <td style="text-align: left">
                        Cash Discount
                    </td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center"></td>
                    <td style="text-align: center">{{$cashAdv + $fuelAdv + $line_charge - $carryingBill - $scalecost}}</td>
                  </tr>
                
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
{{-- <script>
  $(document).ready(function() {
    $('#example1').DataTable({
      dom: 'Bfrtip',
      buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
      ]
    });
  });
</script> --}}
@endsection
