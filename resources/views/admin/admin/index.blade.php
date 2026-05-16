@extends('admin.layouts.admin')

@section('content')

<!-- Content Header -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Admin Management</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admin Users</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="content">
    <div class="container-fluid">

        <!-- Alert Message Container -->
        <div id="messageContainer" class="row mb-3" style="display: none;">
            <div class="col-12">
                <div id="messageBox" class="alert">
                    <div class="d-flex align-items-center">
                        <i id="messageIcon" class="mr-3 fa-2x"></i>
                        <div>
                            <h5 id="messageTitle" class="mb-1"></h5>
                            <p id="messageText" class="mb-0"></p>
                            <ul id="messageList" class="mb-0 mt-1"></ul>
                        </div>
                        <button type="button" class="close ml-auto" onclick="hideMessage()">
                            <span>&times;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4" id="statsSection">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $admins->count() }}</h3>
                        <p>Total Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $roles->count() }}</h3>
                        <p>Total Roles</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $admins->where('status', 1)->count() }}</h3>
                        <p>Active Admins</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{ Auth::user()->role->name }}</h3>
                        <p>Your Role</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-id-badge"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-3" id="actionButtons">
            <div class="col-12">
                <button type="button" class="btn btn-primary" id="newBtn">
                    <i class="fas fa-plus-circle mr-2"></i>Add New Admin
                </button>
                <button type="button" class="btn btn-default ml-2" id="refreshBtn" onclick="location.reload()">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>

        <!-- Add/Edit Form Card -->
        <div class="row mb-4" id="formSection" style="display: none;">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-user-edit mr-2"></i>
                            <span id="formTitle">Add New Admin</span>
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" id="closeFormBtn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Validation Errors -->
                        <div id="validationErrors" class="alert alert-danger" style="display: none;">
                            <h5><i class="icon fas fa-ban"></i> Validation Error!</h5>
                            <ul id="errorList"></ul>
                        </div>

                        <form id="adminForm">
                            @csrf
                            <input type="hidden" id="adminId" name="admin_id" value="">
                            
                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">
                                            <i class="fas fa-user mr-1 text-primary"></i>
                                            Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="name" 
                                               name="name" 
                                               placeholder="Enter first name"
                                               maxlength="255">
                                    </div>
                                </div>

                                <!-- Surname -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="surname">
                                            <i class="fas fa-user mr-1 text-primary"></i>
                                            Surname
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="surname" 
                                               name="surname" 
                                               placeholder="Enter last name"
                                               maxlength="255">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">
                                            <i class="fas fa-envelope mr-1 text-primary"></i>
                                            Email <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               placeholder="Enter email address"
                                               maxlength="255">
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">
                                            <i class="fas fa-phone mr-1 text-primary"></i>
                                            Phone <span class="text-danger">*</span>
                                        </label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="phone" 
                                               name="phone" 
                                               placeholder="Enter phone number"
                                               maxlength="20">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Password -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">
                                            <i class="fas fa-lock mr-1 text-primary"></i>
                                            Password <span class="text-danger" id="passwordRequired">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Enter password"
                                                   minlength="8">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted" id="passwordHelp">Minimum 8 characters</small>
                                    </div>
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="confirm_password">
                                            <i class="fas fa-lock mr-1 text-primary"></i>
                                            Confirm Password <span class="text-danger" id="confirmPasswordRequired">*</span>
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirm_password" 
                                               name="password_confirmation" 
                                               placeholder="Confirm password"
                                               minlength="8">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Role -->
                                <div class="col-md-6" id="roleSection">
                                    <div class="form-group">
                                        <label for="role_id">
                                            <i class="fas fa-user-shield mr-1 text-primary"></i>
                                            Role <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-control select2" id="role_id" name="role_id" style="width: 100%;">
                                            <option value="">Select Role</option>
                                            @foreach($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="status">
                                            <i class="fas fa-toggle-on mr-1 text-primary"></i>
                                            Status
                                        </label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="card card-secondary mt-3">
                                <div class="card-header" style="cursor: pointer;" data-toggle="collapse" data-target="#addressSection">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-map-marker-alt mr-2"></i>Address Information (Optional)
                                        <i class="fas fa-chevron-down float-right"></i>
                                    </h5>
                                </div>
                                <div id="addressSection" class="collapse">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="house_number">House Number</label>
                                                    <input type="text" class="form-control" id="house_number" name="house_number" placeholder="Enter house number">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="street_name">Street Name</label>
                                                    <input type="text" class="form-control" id="street_name" name="street_name" placeholder="Enter street name">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="town">Town/City</label>
                                                    <input type="text" class="form-control" id="town" name="town" placeholder="Enter town or city">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="postcode">Postcode</label>
                                                    <input type="text" class="form-control" id="postcode" name="postcode" placeholder="Enter postcode">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save mr-2"></i>Create Admin
                        </button>
                        <button type="button" class="btn btn-default ml-2" id="cancelBtn">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="reset" class="btn btn-warning ml-2" id="resetBtn">
                            <i class="fas fa-undo mr-2"></i>Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="row" id="tableSection">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list mr-2"></i>All Admin Users
                        </h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search...">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="adminTable" class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th width="50" class="text-center">#</th>
                                        <th>Admin Info</th>
                                        <th>Contact</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th width="120" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($admins as $index => $admin)
                                    <tr>
                                        <td class="text-center">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="mr-3">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong>{{ $admin->name }} {{ $admin->surname }}</strong>
                                                    <br>
                                                    <small class="text-muted">ID: #{{ $admin->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-envelope text-muted mr-1"></i> {{ $admin->email }}
                                            <br>
                                            <i class="fas fa-phone text-muted mr-1"></i> {{ $admin->phone }}
                                        </td>
                                        <td>
                                            @if($admin->role)
                                            <span class="badge badge-primary">{{ $admin->role->name }}</span>
                                            @else
                                            <span class="badge badge-secondary">No Role</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($admin->status ?? 1)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle mr-1"></i>Active
                                            </span>
                                            @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle mr-1"></i>Inactive
                                            </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $admin->created_at->format('d M Y') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $admin->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if(Auth::user()->role->name == "All Access" || Auth::user()->id == $admin->id)
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info btn-sm editBtn" data-id="{{ $admin->id }}" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if(Auth::user()->role->name == "All Access" && Auth::user()->id != $admin->id)
                                                <button type="button" class="btn btn-danger btn-sm deleteBtn" data-id="{{ $admin->id }}" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endif
                                            </div>
                                            @else
                                            <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-users-slash fa-3x text-muted mb-3 d-block"></i>
                                            <h4 class="text-muted">No Admin Found</h4>
                                            <p class="text-muted">Click "Add New Admin" to create one.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Confirm Delete
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center py-3">
                    <i class="fas fa-trash-alt fa-4x text-danger mb-3 d-block"></i>
                    <h4>Are you sure?</h4>
                    <p class="text-muted">This action cannot be undone. The admin account will be permanently deleted.</p>
                    <p><strong id="deleteUserName"></strong></p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fas fa-times mr-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<style>
    .user-avatar .avatar-img {
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    .small-box .icon {
        top: -10px;
    }
    .select2-selection {
        border-radius: 4px !important;
    }
    #messageContainer {
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .alert {
        border-radius: 6px;
        border-left: 5px solid;
    }
    .alert-success {
        border-left-color: #28a745;
        background-color: #f0fff4;
        color: #155724;
    }
    .alert-danger {
        border-left-color: #dc3545;
        background-color: #fff5f5;
        color: #721c24;
    }
    .alert-warning {
        border-left-color: #ffc107;
        background-color: #fffbeb;
        color: #856404;
    }
    .alert-info {
        border-left-color: #17a2b8;
        background-color: #f0f9ff;
        color: #0c5460;
    }
    .close {
        opacity: 0.5;
    }
    .close:hover {
        opacity: 1;
    }
</style>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Select an option'
        });

        // Initialize DataTable
        var table = $('#adminTable').DataTable({
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search admins...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ admins",
                infoEmpty: "No admins found",
                emptyTable: "No admin data available",
                zeroRecords: "No matching admins found"
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="fas fa-copy"></i> Copy'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="fas fa-file-csv"></i> CSV'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="fas fa-file-excel"></i> Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="fas fa-file-pdf"></i> PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="fas fa-print"></i> Print'
                },
                {
                    extend: 'colvis',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="fas fa-columns"></i> Columns'
                }
            ],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });

        // AJAX Setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // URLs
        const storeUrl = "{{ route('admin.store') }}";
        const baseUrl = "{{ route('alladmin') }}";
        let isEditMode = false;
        let deleteId = null;
        let messageTimeout = null;

        // Show Message Function
        function showMessage(type, title, text, list) {
            // Clear any existing timeout
            if (messageTimeout) {
                clearTimeout(messageTimeout);
            }

            const container = $('#messageContainer');
            const messageBox = $('#messageBox');
            const icon = $('#messageIcon');
            const titleEl = $('#messageTitle');
            const textEl = $('#messageText');
            const listEl = $('#messageList');

            // Remove all alert classes
            messageBox.removeClass('alert-success alert-danger alert-warning alert-info');
            
            // Set type-specific styles
            switch(type) {
                case 'success':
                    messageBox.addClass('alert-success');
                    icon.addClass('fas fa-check-circle text-success');
                    break;
                case 'error':
                    messageBox.addClass('alert-danger');
                    icon.addClass('fas fa-times-circle text-danger');
                    break;
                case 'warning':
                    messageBox.addClass('alert-warning');
                    icon.addClass('fas fa-exclamation-triangle text-warning');
                    break;
                case 'info':
                    messageBox.addClass('alert-info');
                    icon.addClass('fas fa-info-circle text-info');
                    break;
            }

            titleEl.text(title);
            textEl.text(text);
            listEl.empty();

            if (list && Object.keys(list).length > 0) {
                $.each(list, function(key, value) {
                    listEl.append('<li>' + value + '</li>');
                });
            }

            container.show();

            // Scroll to message
            $('html, body').animate({
                scrollTop: container.offset().top - 50
            }, 300);

            // Auto-hide after 5 seconds
            messageTimeout = setTimeout(function() {
                hideMessage();
            }, 5000);
        }

        // Hide Message Function
        window.hideMessage = function() {
            const container = $('#messageContainer');
            container.fadeOut(300, function() {
                $('#messageList').empty();
            });
            if (messageTimeout) {
                clearTimeout(messageTimeout);
                messageTimeout = null;
            }
        };

        // Show Form
        $('#newBtn').click(function() {
            resetForm();
            isEditMode = false;
            $('#formTitle').text('Add New Admin');
            $('#submitBtn').html('<i class="fas fa-save mr-2"></i>Create Admin');
            $('#passwordRequired, #confirmPasswordRequired').show();
            $('#password, #confirm_password').prop('required', true);
            $('#passwordHelp').text('Minimum 8 characters');
            $('#formSection').slideDown(300);
            $('#statsSection, #tableSection').slideUp(200);
            $('html, body').animate({
                scrollTop: $("#formSection").offset().top - 50
            }, 300);
        });

        // Hide Form
        function hideForm() {
            $('#formSection').slideUp(200);
            $('#statsSection, #tableSection').slideDown(300);
            resetForm();
        }

        $('#closeFormBtn, #cancelBtn').click(hideForm);

        // Reset Form
        function resetForm() {
            $('#adminForm')[0].reset();
            $('#adminId').val('');
            $('.select2').val('').trigger('change');
            hideValidationErrors();
            isEditMode = false;
        }

        $('#resetBtn').click(function() {
            resetForm();
            showMessage('info', 'Info', 'Form has been reset.');
        });

        // Toggle Password Visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const icon = $(this).find('i');
            
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Show Validation Errors
        function showValidationErrors(errors) {
            const errorList = $('#errorList');
            errorList.empty();
            
            $.each(errors, function(key, value) {
                errorList.append('<li>' + value + '</li>');
            });
            
            $('#validationErrors').slideDown(300);
            $('html, body').animate({
                scrollTop: $("#validationErrors").offset().top - 100
            }, 300);
        }

        // Hide Validation Errors
        function hideValidationErrors() {
            $('#validationErrors').slideUp(200);
            $('#errorList').empty();
        }

        // Submit Form
        $('#submitBtn').click(function() {
            hideValidationErrors();
            hideMessage();
            
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Saving...');

            const formData = new FormData($('#adminForm')[0]);
            
            let url, method;
            
            if (isEditMode) {
                const adminId = $('#adminId').val();
                url = baseUrl + '/' + adminId;
                formData.append('_method', 'PUT');
                method = 'POST';
            } else {
                url = storeUrl;
                method = 'POST';
            }

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (response.status === 200) {
                        showMessage('success', 'Success', response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const errorMessages = {};
                        $.each(errors, function(key, value) {
                            errorMessages[key] = value[0];
                        });
                        showValidationErrors(errorMessages);
                        showMessage('warning', 'Validation Error', 'Please fix the errors below.', errorMessages);
                    } else if (xhr.status === 403) {
                        showMessage('error', 'Permission Denied', xhr.responseJSON.message);
                    } else {
                        showMessage('error', 'Error', 'Something went wrong. Please try again.');
                        console.log(xhr);
                    }
                }
            });
        });

        // Edit Admin
        $(document).on('click', '.editBtn', function() {
            const adminId = $(this).data('id');
            const editUrl = baseUrl + '/' + adminId + '/edit';

            showMessage('info', 'Loading', 'Please wait while loading admin data...');

            $.get(editUrl, function(response) {
                if (response.status === 200) {
                    const admin = response.data;
                    
                    isEditMode = true;
                    $('#formTitle').text('Edit Admin - ' + admin.name);
                    $('#submitBtn').html('<i class="fas fa-save mr-2"></i>Update Admin');
                    
                    $('#adminId').val(admin.id);
                    $('#name').val(admin.name);
                    $('#surname').val(admin.surname || '');
                    $('#email').val(admin.email);
                    $('#phone').val(admin.phone);
                    $('#role_id').val(admin.role_id).trigger('change');
                    $('#house_number').val(admin.house_number || '');
                    $('#street_name').val(admin.street_name || '');
                    $('#town').val(admin.town || '');
                    $('#postcode').val(admin.postcode || '');
                    $('#status').val(admin.status ?? 1);
                    
                    // Password optional in edit mode
                    $('#passwordRequired, #confirmPasswordRequired').hide();
                    $('#password, #confirm_password').prop('required', false);
                    $('#passwordHelp').text('Leave blank to keep current password');
                    
                    // Show/hide role based on current user role
                    const userRoleId = {{ Auth::user()->role_id ?? 'null' }};
                    if (userRoleId != 1) {
                        $('#roleSection').hide();
                    } else {
                        $('#roleSection').show();
                    }

                    // Show form
                    $('#formSection').slideDown(300);
                    $('#statsSection, #tableSection').slideUp(200);
                    $('html, body').animate({
                        scrollTop: $("#formSection").offset().top - 50
                    }, 300);

                    hideMessage();
                }
            }).fail(function(xhr) {
                if (xhr.status === 403) {
                    showMessage('error', 'Permission Denied', 'You do not have permission to edit.');
                } else {
                    showMessage('error', 'Error', 'Failed to load admin data.');
                }
            });
        });

        // Delete Admin
        $(document).on('click', '.deleteBtn', function() {
            deleteId = $(this).data('id');
            const adminName = $(this).closest('tr').find('strong').text();
            $('#deleteUserName').text(adminName);
            $('#deleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(function() {
            if (!deleteId) return;
            
            const btn = $(this);
            const originalText = btn.html();
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Deleting...');

            const deleteUrl = baseUrl + '/' + deleteId;
            
            $.ajax({
                url: deleteUrl,
                type: 'POST',
                data: {
                    _method: 'DELETE'
                },
                success: function(response) {
                    btn.prop('disabled', false).html(originalText);
                    $('#deleteModal').modal('hide');
                    
                    if (response.success) {
                        showMessage('success', 'Success', response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showMessage('error', 'Error', response.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalText);
                    $('#deleteModal').modal('hide');
                    
                    if (xhr.status === 403) {
                        showMessage('error', 'Permission Denied', xhr.responseJSON.message);
                    } else {
                        showMessage('error', 'Error', 'Failed to delete admin.');
                    }
                }
            });
        });

        // Prevent form submission on Enter
        $('#adminForm').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#submitBtn').click();
            }
        });
    });
</script>
@endsection