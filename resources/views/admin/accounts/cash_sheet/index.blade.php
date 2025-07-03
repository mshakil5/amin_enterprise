@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3">
  <div class="container-fluid">
      <div class="page-header d-flex justify-content-between">
          <a href="{{ url()->previous() }}" class="btn btn-secondary mb-2">Back</a>
          <button onclick="window.print();" class="btn btn-info mb-2">Print</button>
      </div>

      <div class="row print-area">
          <div class="col-12">
              <div class="card card-secondary">

                  <div class="card-body">
                      <h1 class="text-center">M/S AMIN ENTERPRISE </h1>
                      <h2 class="text-center">BSRM PROGRAM </h2>
                      <h3 class="text-center">Cash Sheet({{ date('d-m-Y') }})</h3>
                      <div class="card card-primary mt-3">
                          <table class="table table-bordered table-hover">
                              <thead>
                                  <tr>
                                      <th colspan="6" class="text-center">DEBIT</th>
                                      <th colspan="6" class="text-center">CREDIT</th>
                                  </tr>
                              </thead>
                              <tbody>

                                  <tr>
                                      <td>Date</td>
                                      <td>Vch No</td>
                                      <td>Cheque No</td>
                                      <td class="text-center">Particulars</td>
                                      <td>Cash(Taka)</td>
                                      <td>Bank</td>

                                      <td>Date</td>
                                      <td>Vch No</td>
                                      <td>Cheque No</td>
                                      <td>Particulars</td>
                                      <td>Cash(Taka)</td>
                                      <td>Bank</td>
                                  </tr>
                                  <tr>
                                      <td>21-06-25</td>
                                      <td></td>
                                      <td></td>
                                      <td>Cash In Hand(Opening Balance)</td>
                                      <td>341638</td>
                                      <td></td>
                                      <td>21-06-25</td>
                                      <td></td>
                                      <td></td>
                                      <td>Cash In Hand(Opening Balance)</td>
                                      <td>341638</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>21-06-25</td>
                                      <td></td>
                                      <td></td>
                                      <td>Cash In Hand(Opening Balance)</td>
                                      <td>341638</td>
                                      <td></td>
                                      <td>21-06-25</td>
                                      <td></td>
                                      <td></td>
                                      <td>Cash In Hand(Opening Balance)</td>
                                      <td>341638</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td>21-06-25</td>
                                      <td></td>
                                      <td></td>
                                      <td>Cash In Hand(Opening Balance)</td>
                                      <td>341638</td>
                                      <td></td>
                                      <td>21-06-25</td>
                                      <td></td>
                                      <td></td>
                                      <td>Cash In Hand(Opening Balance)</td>
                                      <td>341638</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td colspan="3">Total Receipts</td>
                                      <td>-</td>
                                      <td>341638</td>
                                      <td></td>
                                      <td colspan="3">Total Payments</td>
                                      <td>-</td>
                                      <td>341638</td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td colspan="4">Total</td>
                                      <td>341638</td>
                                      <td></td>
                                      <td colspan="4">Total</td>
                                      <td>341638</td>
                                      <td></td>
                                  </tr>
                              </tbody>
                          </table>
                          <div class="row mt-5">
                              <div class="col-md-3">
                                  <p class="text-center">Prepared By</p>
                              </div>
                              <div class="col-md-3">
                                  <p class="text-center">Checked By</p>
                              </div>
                              <div class="col-md-3">
                                  <p class="text-center">Approved By</p>
                              </div>
                              <div class="col-md-3">
                                  <p class="text-center">Managing Director</p>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
</section>

@endsection

@section('script')

@endsection