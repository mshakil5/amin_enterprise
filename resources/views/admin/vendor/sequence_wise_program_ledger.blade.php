@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="page-header"><a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a></div>
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h4>Ledger</h4>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        <div class="text-center mb-4 company-name-container">
                                @if ($vendor)
                                    <h4>{{ $vendor->name }} Ledger</h4>
                                    <h5>{{ $vendorSequenceNumber->unique_id}}</h5>
                                @else
                                    <h4>Account Name Not Found</h4>
                                @endif
                        </div>

                        
                            <table id="dataTransactionsTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th  style="text-align: center">Date</th>
                                        <th  style="text-align: center; width:50%">Description</th>
                                        <th  style="text-align: center; width:10%">Ref</th>
                                        <th  style="text-align: center">Debit</th>
                                        <th  style="text-align: center">Credit</th>
                                        <th  style="text-align: center">Voucher</th>                                
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td></td>
                                        <td>Previous balance</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td>Vendor payable</td>
                                        <td></td>
                                        <td style="text-align: right"></td>
                                        <td style="text-align: right">
                                            {{ number_format($summary->total_carrying_bill + $summary->total_scale_fee - $advanceData->total_cashamount - $advanceData->total_fuelamount, 2)}}
                                        </td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td>Vendors payment</td>
                                        <td></td>
                                        <td style="text-align: right">{{ number_format($totalPaidTransaction, 2) }}</td>
                                        <td style="text-align: right">
                                        </td>
                                        <td></td>
                                    </tr>

                                    
                                </tbody>
                            </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@endsection

@section('script')

<script>
    $(function () {
      $("#dataTransactionsTable").DataTable({
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
@endsection
