@extends('parent.full')
@section('css')
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-evenly mb-3">
                <div class="col">
                    Category Management
                </div>
                <div class="col text-end">
                    <button class="btn btn-success add-category" data-bs-target="#modal-category" data-bs-toggle="modal">Add New Category</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table id="table-category" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Action</th>
                            <th>Code</th>
                            <th>Name</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-category" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel4">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-category">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" id="code" name="code" class="form-control" placeholder="Enter code">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter name">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="save-category">Submit</button>
                    <button type="button" class="btn btn-warning d-none" id="update-category">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        window.categoryDataTable;

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idcategory = $(this).data("category");
                $("#update-category").data("category", idcategory);
                if (window.categoryDataTable.rows('.selected').data().length == 0) {
                    $('#table-category tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.categoryDataTable.rows('.selected').data()[0];

                $('#modal-category').modal('show');
                $('#modal-category').find('.modal-title').html(`Edit Category`);
                $('#save-category').addClass('d-none');
                $('#update-category').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('category-management.show') }}/" + idcategory,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-category').find("form")
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
            $('.delete').click(function() {
                if (window.categoryDataTable.rows('.selected').data().length == 0) {
                    $('#table-event tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idData = $(this).data("category");
                var data = window.categoryDataTable.rows('.selected').data()[0];
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('category-management.destroy') }}/" +
                        idData,
                    data: {
                        _token: `{{ csrf_token() }}`,
                    },
                    dataType: "json",
                    success: function(response) {
                        window.categoryDataTable.ajax.reload();
                    },
                    error: function(error) {}
                });
            });
        }
        $(function() {
            window.categoryDataTable = $('#table-category').DataTable({
                ajax: "{{ route('category-management.data-table') }}",
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
                    name: 'code',
                    data: 'code',
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
                }]
            });
            window.categoryDataTable.on('draw.dt', function() {
                actionData();
            });
            $('#save-category').click(function() {
                let data = serializeObject($('#form-category'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('category-management.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-category').modal('hide')
                        window.categoryDataTable.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-category .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-category').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                    }
                });
            });
            $('#update-category').click(function() {
                let data = serializeObject($('#form-category'));
                $.ajax({
                    type: "PATCH",
                    url: `{{ route('category-management.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-category').modal('hide')
                        window.categoryDataTable.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-category .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-category').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                    }
                });
            });
            $('#modal-category').on('hidden.bs.modal', function() {
                window.state = 'add';
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add Category`);
                $('#save-category').removeClass('d-none');
                $('#update-category').addClass('d-none');
                $('#modal-category .is-invalid').removeClass('is-invalid')
                $('#table-category tbody').find('tr').removeClass('selected');
            });
        });
    </script>
@endsection
