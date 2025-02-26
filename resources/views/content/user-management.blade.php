@extends('parent.full')
@section('css')
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-evenly mb-3">
                <div class="col">
                    User Management
                </div>
                <div class="col text-end">
                    <button class="btn btn-success add-user" data-bs-target="#modal-user" data-bs-toggle="modal">Add New User</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table id="table-user" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Action</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>No</td>
                            <td>Action</td>
                            <td>Username</td>
                            <td>Name</td>
                            <td>Email</td>
                            <td>Role</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-user" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel4">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-user">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" name="role" id="role">
                                    <option value="">Choose One</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Operator">Operator</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="username" class="form-label">username</label>
                                <input type="text" id="username" name="username" class="form-control" placeholder="Enter Username">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter Password">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" id="email" name="email" class="form-control" placeholder="Enter Email">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="save-user">Submit</button>
                    <button type="button" class="btn btn-warning d-none" id="update-user">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        window.userDataTable;

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let iduser = $(this).data("user");
                $("#update-user").data("user", iduser);
                if (window.userDataTable.rows('.selected').data().length == 0) {
                    $('#table-user tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.userDataTable.rows('.selected').data()[0];

                $('#modal-user').modal('show');
                $('#modal-user').find('.modal-title').html(`Edit User`);
                $('#save-user').addClass('d-none');
                $('#update-user').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('user-management.show') }}/" + iduser,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-user').find("form")
                            .find('input, select').map(function(index, element) {
                                if (response.data[element.name]) {
                                    $(`[name=${element.name}]`).val(response.data[element
                                        .name])
                                }
                            });
                    },
                    error: function(error) {}
                });
            });
            $('.lock').click(function() {
                if (window.userDataTable.rows('.selected').data().length == 0) {
                    $('#table-event tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idData = $(this).data("user");
                var data = window.userDataTable.rows('.selected').data()[0];
                $.ajax({
                    type: "PATCH",
                    url: "{{ route('user-management.lock') }}/" +
                        idData,
                    data: {
                        _token: `{{ csrf_token() }}`,
                    },
                    dataType: "json",
                    success: function(response) {
                        window.userDataTable.ajax.reload();
                    },
                    error: function(error) {}
                });
            });
            $('.delete').click(function() {
                if (window.userDataTable.rows('.selected').data().length == 0) {
                    $('#table-event tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idData = $(this).data("user");
                var data = window.userDataTable.rows('.selected').data()[0];
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('user-management.destroy') }}/" +
                        idData,
                    data: {
                        _token: `{{ csrf_token() }}`,
                    },
                    dataType: "json",
                    success: function(response) {
                        window.userDataTable.ajax.reload();
                    },
                    error: function(error) {}
                });
            });
        }
        $(function() {
            window.userDataTable = $('#table-user').DataTable({
                ajax: "{{ route('user-management.data-table') }}",
                processing: true,
                serverSide: true,
                order: [
                    [2, 'desc']
                ],
                columnDefs: [{
                    searchBuilder: {
                        defaultCondition: '='
                    },
                    targets: [1]
                }],
                layout: {
                    top1: 'searchBuilder'
                },
                columns: [{
                    target: 0,
                    name: 'number',
                    data: 'number',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 1,
                    name: 'action',
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => {
                        return `<div class='d-flex gap-2'>${data}</div>`
                    }
                }, {
                    target: 2,
                    name: 'username',
                    data: 'username',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 3,
                    name: 'name',
                    data: 'name',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 4,
                    name: 'email',
                    data: 'email',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 5,
                    name: 'role',
                    data: 'role',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }]
            });
            window.userDataTable.on('draw.dt', function() {
                actionData();
            });
            $('#save-user').click(function() {
                let data = serializeObject($('#form-user'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('user-management.register') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-user').modal('hide')
                        window.userDataTable.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-user .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-user').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                    }
                });
            });
            $('#update-user').click(function() {
                let data = serializeObject($('#form-user'));
                $.ajax({
                    type: "PATCH",
                    url: `{{ route('user-management.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-user').modal('hide')
                        window.userDataTable.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-user .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-user').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                    }
                });
            });
            $('#modal-user').on('hidden.bs.modal', function() {
                window.state = 'add';
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add User`);
                $('#save-user').removeClass('d-none');
                $('#update-user').addClass('d-none');
                $('#modal-user .is-invalid').removeClass('is-invalid')
                $('#table-user tbody').find('tr').removeClass('selected');
            });
        });
    </script>
@endsection
