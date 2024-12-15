@extends('admin.layouts.admin')

@section('content')


<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
        <div class="row justify-content-md-center">
            <div class="col-md-10">
                <div class="card card-secondary">
                    <div class="card-header">
                        <h3 class="card-title">
                            
                                <h4>Profit and Loss Statement</h4>
                                
                        </h3>
                    </div>
                    <div class="card-body">
                        <div id="alert-container"></div>

                        
                        <div class="text-center mb-4 company-name-container">
                            @php
                                $val = 0;
                            @endphp
                        </div>

                        <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Particulars</th>
                                        <th>ref</th>
                                        <th>Amount</th>                        
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><h5>Revenue from Service</h5></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Received</td>
                                        <td></td>
                                        <td>{{number_format($totalReceive, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td>Token fee</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td style="text-align: right">Total Revenue</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td>Less: Carrying cost</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td style="text-align: right">Gross Profit</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td>Less: Admin & Operating Expense</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td>all expense here</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td style="text-align: right">Operating Profit/Loss before VAT & Commission</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>


                                    <tr>
                                        <td>Less - Vat @ 2.5%</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Less - Commission @ Tk. 10 for CDDJ, CTG PORT & @Tk. 20 for KDDJ</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>

                                    <tr>
                                        <td style="text-align: right">Net operating profit or loss after VAT & Commission</td>
                                        <td></td>
                                        <td>{{number_format($val, 2)}}</td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>

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
      $("#example12").DataTable({
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

@endsection
