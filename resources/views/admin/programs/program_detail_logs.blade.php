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
                          <th>Program ID</th>
                          <th>Action</th>
                      </tr>
                  </thead>
                  <tbody>
                      @foreach ($logs as $key => $log)
                          <tr>
                              <td>{{ $key + 1 }}</td>
                              <td>{{ $log->causer?->name ?? '' }}</td>
                              <td>{{ $log->subject?->programid ?? '' }}</td>
                              <td>
                                  <button 
                                    class="btn btn-sm btn-info show-changes-btn" 
                                    data-changes='@json($log->changes)' 
                                    data-causer="{{ $log->causer?->name ?? '' }}"
                                    data-programid="{{ $log->subject?->programid ?? '' }}"
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
              <th>Field</th>
              <th>Old Value</th>
              <th>New Value</th>
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
      const changes = $(this).data('changes');
      const causer = $(this).data('causer');
      const programId = $(this).data('programid');

      $('#changedByInfo').text(`Performed By: ${causer}`);
      $('#programIdInfo').text(`Program ID: ${programId}`);

      const tbody = $('#changesTable tbody');
      tbody.empty();

      if (!changes || !changes.attributes) {
        tbody.append('<tr><td colspan="3" class="text-center">No changes recorded</td></tr>');
      } else {
        Object.keys(changes.attributes).forEach(field => {
          const newVal = changes.attributes[field];
          const oldVal = changes.old ? changes.old[field] : '-';

          tbody.append(`
            <tr>
              <td>${field}</td>
              <td>${oldVal !== null ? oldVal : '-'}</td>
              <td>${newVal !== null ? newVal : '-'}</td>
            </tr>
          `);
        });
      }

      if ($.fn.DataTable.isDataTable('#changesTable')) {
        $('#changesTable').DataTable().destroy();
      }

      changesTable = $('#changesTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 10,
        lengthChange: true,
      });
    });

    $('#changesModal').on('hidden.bs.modal', function () {
      if ($.fn.DataTable.isDataTable('#changesTable')) {
        $('#changesTable').DataTable().destroy();
      }
      $('#changedByInfo').text('');
      $('#programIdInfo').text('');
    });
  });
</script>
@endsection
