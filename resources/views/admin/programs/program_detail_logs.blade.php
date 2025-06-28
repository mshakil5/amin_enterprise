@extends('admin.layouts.admin')

@section('content')

<section class="content pt-3" id="contentContainer">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">

          <div class="card card-secondary">
            <div class="card-header">
              <h3 class="card-title">All Data</h3>
            </div>
            <div class="card-body">
              <table id="example1" class="table table-bordered table-striped">
                  <thead>
                      <tr>
                          <th colspan="5" class="text-center bg-light">Program Detail Log Summary (Last 2 Days)</th>
                      </tr>
                      <tr>
                          <th>Sl</th>
                          <th>Performed By</th>
                          <th>Counter</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach ($logs as $key => $log)
                          <tr>
                              <td>{{ $key + 1 }}</td>
                              <td>{{ $log->first()['causer_name'] ?? '' }}</td>
                              <td>{{ $log->count() }}</td>
                              <td>
                                <button 
                                  class="btn btn-sm btn-info show-changes-btn my-1"
                                  data-logs='@json($log)'
                                  data-causer="{{ $log->first()['causer_name'] ?? '' }}"
                                  data-toggle="modal" 
                                  data-target="#changesModal">
                                  View
                                </button>
                              </td>
                          </tr>
                      @endforeach
                  </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
</section>

<!-- Changes Modal -->
<div class="modal fade" id="changesModal" tabindex="-1" role="dialog" aria-labelledby="changesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changesModalLabel">
          <small class="ml-3 text-muted" id="changedByInfo"></small>
          <small class="ml-3 text-muted" id="programIdInfo"></small>
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered" id="changesTable">
          <thead>
            <tr>
              <th>Header ID</th>
              <th>Dest Qty</th>
              <th>Challan Number</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
  $(document).ready(function () {
    $('#example1').DataTable({
      pageLength: 100
    });

    let changesTable;

    $('.show-changes-btn').on('click', function () {
      const logs = $(this).data('logs');
      const causer = $(this).data('causer');

      $('#changedByInfo').text(`Performed By: ${causer}`);

      const tbody = $('#changesTable tbody');
      tbody.empty();

      if (!logs.length) {
        tbody.append('<tr><td colspan="3" class="text-center">No relevant changes found</td></tr>');
        return;
      }

      logs.forEach(log => {
        if (log.headerid === '-' && log.dest_qty === '-' && log.challan_no === '-') return;

        tbody.append(`
          <tr>
            <td>${log.headerid}</td>
            <td>${log.dest_qty}</td>
            <td>${log.challan_no}</td>
          </tr>
        `);
      });
    });

    $('#changesModal').on('hidden.bs.modal', function () {
      $('#changedByInfo').text('');
      $('#programIdInfo').text('');
    });
  });
</script>
@endsection