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
            <h1 class="text-center">M/S AMIN ENTERPRISE</h1>
            <h2 class="text-center">BSRM PROGRAM</h2>
            <h3 class="text-center">Cash Sheet ({{ date('d-m-Y') }})</h3>

            <div class="row mt-3">
              <div class="col-6 p-0">
                <div class="card card-primary mb-0">
                  <h4 class="text-center pt-2">DEBIT</h4>
                  <table class="table table-bordered table-sm mb-0">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Vch No</th>
                        <th>Cheque No</th>
                        <th>Particulars</th>
                        <th>Cash(Taka)</th>
                        <th>Bank</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>{{ date('d-m-Y') }}</td>
                        <td></td>
                        <td></td>
                        <td>Cash In Hand (Opening Balance)</td>
                        <td class="text-right">{{ $cashInHand }}</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Cash In Field(Opening Balance)</td>
                        <td class="text-right">{{ $cashInField }}</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>Petty Cash(Entertainment)</td>
                        <td class="text-right">{{ $pettyCash }}</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="bg-danger text-white">Suspense A/C</td>
                        <td class="text-right">{{ $suspenseAc }}</td>
                        <td></td>
                      </tr>

                      @foreach ($liabilities as $liability)
                        <tr>
                          <td>{{ \Carbon\Carbon::parse($liability->date)->format('d-m-Y') }}</td>
                          <td>{{ $liability->tran_id }}</td>
                          <td></td>
                          <td>{{ $liability->description }}</td>
                          <td class="text-right">{{ $liability->amount }}</td>
                          <td></td>
                        </tr>
                        
                      @endforeach

                      <tr>
                        <td colspan="3">Total Receipts</td>
                        <td>{{ $totalReceipts }}</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="4">Total</td>
                        <td>341638</td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="col-6 p-0">
                <div class="card card-primary mb-0">
                  <h4 class="text-center pt-2">CREDIT</h4>
                  <table class="table table-bordered table-sm mb-0">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Vch No</th>
                        <th>Cheque No</th>
                        <th>Particulars</th>
                        <th>Cash(Taka)</th>
                        <th>Bank</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>{{ date('d-m-Y') }}</td>
                        <td></td>
                        <td></td>
                        <td>Cash In Hand (Opening Balance)</td>
                        <td>341638</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td>25-06-2025</td>
                        <td></td>
                        <td></td>
                        <td>Example Credit Entry</td>
                        <td>20000</td>
                        <td>ABC Bank</td>
                      </tr>
                      <tr>
                        <td colspan="4">Total Payments</td>
                        <td>341638</td>
                        <td></td>
                      </tr>
                      <tr>
                        <td colspan="4">Total</td>
                        <td>341638</td>
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <div class="row mt-5">
              <div class="col-md-3 text-center">
                <p>Prepared By</p>
              </div>
              <div class="col-md-3 text-center">
                <p>Checked By</p>
              </div>
              <div class="col-md-3 text-center">
                <p>Approved By</p>
              </div>
              <div class="col-md-3 text-center">
                <p>Managing Director</p>
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