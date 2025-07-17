@extends('admin.layouts.admin')

@section('content')
<section class="content pt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
      </a>
    </div>

  <div class="container-fluid">
    <div class="card card-danger">
      <div class="card-header">
        <h3 class="card-title">Deleted Program Details</h3>
      </div>
      <div class="card-body">
        <table id="deletedDetailsTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>SL</th>
              <th>Date</th>
              <th>Deleted By</th>
              <th>Consignment No</th>
              <th>Truck No</th>
              <th>Challan No</th>
              <th>Client</th>
              <th>Vendor</th>
              <th>Ghat</th>
              <th>Mother Vassel</th>
              <th>Lighter Vassel</th>
              <th>Note</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @foreach($deletedDetails as $key => $detail)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>{{ \Carbon\Carbon::parse($detail->date)->format('d/m/Y') }}</td>
              <td>{{ $detail->deleteLogs->first()?->causer?->name ?? 'Unknown' }}</td>
              <td>{{ $detail->consignmentno }}</td>
              <td>{{ $detail->truck_number }}</td>
              <td>{{ $detail->challan_no }}</td>
              <td>{{ $detail->client->name ?? '' }}</td>
              <td>{{ $detail->vendor->name ?? '' }}</td>
              <td>{{ $detail->ghat->name ?? '' }}</td>
              <td>{{ $detail->motherVassel->name ?? '' }}</td>
              <td>{{ $detail->lighterVassel->name ?? '' }}</td>
              <td>{{ Str::limit($detail->note, 50) }}</td>
              <td>
                @if($detail->status == 1)
                  <span class="badge badge-primary">Processing</span>
                @elseif($detail->status == 0)
                  <span class="badge badge-danger">Cancelled</span>
                @elseif($detail->status == 2)
                  <span class="badge badge-warning">Hold</span>
                @elseif($detail->status == 3)
                  <span class="badge badge-success">Complete</span>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
@endsection

@section('script')
<script>
  $(function () {
    $('#deletedDetailsTable').DataTable({
      "responsive": true,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#deletedDetailsTable_wrapper .col-md-6:eq(0)');
  });
</script>
@endsection