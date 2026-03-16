@extends('admin.layouts.admin')

@section('content')
    <section class="content mt-3">
        <div class="container-fluid">
            <div id="newBtnSection">
                <button type="button" class="btn btn-primary my-3" id="newBtn">
                    <i class="fas fa-plus"></i> Add New Note
                </button>
            </div>
        </div>
    </section>

    <section class="content" id="addThisFormContainer" style="display: none;">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-outline card-secondary shadow">
                        <div class="card-header">
                            <h3 class="card-title" id="formTitle">Add New Note</h3>
                        </div>
                        <form id="createThisForm">
                            @csrf
                            <div class="card-body">
                                <div class="ermsg"></div>
                                <input type="hidden" id="codeid" name="codeid">
                                <input type="hidden" id="vendor_id" name="vendor_id" value="{{ $vendor->id }}">
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="date">Expiry Date</label>
                                            <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">Note Details</label>
                                            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter note here..." required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <button type="button" id="FormCloseBtn" class="btn btn-default mr-2">Cancel</button>
                                <button type="submit" id="addBtn" class="btn btn-secondary" value="Create">Create Note</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content" id="contentContainer">
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h3 class="card-title"><strong>{{ $vendor->name }}</strong> - Vendor Notes</h3>
                </div>
                <div class="card-body">
                    <table id="example1" class="table table-hover table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th width="50">Sl</th>
                                <th width="150">Date</th>
                                <th>Note</th>
                                <th width="80" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->date)->format('d-M-Y') }}</td>
                                <td>{{ $item->note }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-info edit-btn" data-id="{{ $item->id }}">
                                        <i class="fa fa-edit"></i>
                                    </button>
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
    $(document).ready(function () {
        // Initialize DataTable
        const table = $("#example1").DataTable({
            "responsive": true, 
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print"]
        });
        table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

        // Configuration
        const URLS = {
            store: "{{ URL::to('/admin/vendor-note') }}",
            update: "{{ URL::to('/admin/vendor-note-update') }}"
        };

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Helper: Toggle Form Visibility
        function toggleForm(show = true) {
            if (show) {
                $("#newBtnSection").fadeOut(100);
                $("#addThisFormContainer").fadeIn(300);
            } else {
                $("#addThisFormContainer").fadeOut(200);
                $("#newBtnSection").fadeIn(100);
                clearForm();
            }
        }

        function clearForm() {
            $('#createThisForm')[0].reset();
            $("#codeid").val('');
            $("#addBtn").val('Create').html('Create Note');
            $("#formTitle").text('Add New Note');
            $(".ermsg").html('');
        }

        // Click Events
        $("#newBtn").click(() => toggleForm(true));
        $("#FormCloseBtn").click(() => toggleForm(false));

        // Submit Form (Handles both Create and Update)
        $("#createThisForm").submit(function(e) {
            e.preventDefault();
            const btn = $("#addBtn");
            const isUpdate = btn.val() === 'Update';
            const targetUrl = isUpdate ? URLS.update : URLS.store;

            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

            $.ajax({
                url: targetUrl,
                method: "POST",
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function (d) {
                    if (d.status == 300) {
                        $(".ermsg").html(`<div class="alert alert-success">${d.message}</div>`);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        $(".ermsg").html(`<div class="alert alert-danger">${d.message}</div>`);
                        btn.prop('disabled', false).html(isUpdate ? 'Update Note' : 'Create Note');
                    }
                },
                error: function (xhr) {
                    console.error(xhr);
                    btn.prop('disabled', false).html('Error. Try Again');
                }
            });
        });

        // Edit Button Click
        $("#contentContainer").on('click', '.edit-btn', function() {
            const id = $(this).data('id');
            const editUrl = `${URLS.store}/${id}/edit`;

            $.get(editUrl, function(data) {
                $("#date").val(data.date);
                $("#description").val(data.note);
                $("#codeid").val(data.id);
                
                $("#formTitle").text('Edit Note');
                $("#addBtn").val('Update').html('Update Note');
                toggleForm(true);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    });
</script>
@endsection