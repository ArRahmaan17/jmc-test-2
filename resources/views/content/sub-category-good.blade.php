@extends('parent.full')
@section('css')
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-evenly mb-3">
                <div class="col">
                    Sub Category Management
                </div>
                <div class="col text-end">
                    <button class="btn btn-success add-sub-category" data-bs-target="#modal-sub-category" data-bs-toggle="modal">Add New Sub Category</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table id="table-sub-category" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Action</th>
                            <th>Name</th>
                            <th>Price Limit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-sub-category" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel4">Add Sub Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-sub-category">
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="categoryId" class="form-label">Category</label>
                                <select class="form-select" name="categoryId" id="categoryId">
                                    <option value="">Choose One</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">({{ $category->code }}) {{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="name" class="form-label">name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Enter name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="price_limit" class="form-label">price limit</label>
                                <input type="text" id="price_limit" name="price_limit" class="form-control price" placeholder="Enter price limit">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="save-sub-category">Submit</button>
                    <button type="button" class="btn btn-warning d-none" id="update-sub-category">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.categoryDataTable;

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                console.log($(this).data())
                let idcategory = $(this).data("subCategory");
                $("#update-sub-category").data("sub-category", idcategory);
                if (window.categoryDataTable.rows('.selected').data().length == 0) {
                    $('#table-sub-category tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.categoryDataTable.rows('.selected').data()[0];

                $('#modal-sub-category').modal('show');
                $('#modal-sub-category').find('.modal-title').html(`Edit Sub Category`);
                $('#save-sub-category').addClass('d-none');
                $('#update-sub-category').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('sub-category-management.show') }}/" + idcategory,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-sub-category').find("form")
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
                let idData = $(this).data("subCategory");
                var data = window.categoryDataTable.rows('.selected').data()[0];
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('sub-category-management.destroy') }}/" +
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
            window.categoryDataTable = $('#table-sub-category').DataTable({
                ajax: "{{ route('sub-category-management.data-table') }}",
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
                    name: 'name',
                    data: 'name',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 3,
                    name: 'price_limit',
                    data: 'price_limit',
                    orderable: true,
                    searchable: true,
                    render: DataTable.render.number('.', null, 0, 'Rp. ')
                }]
            });
            window.categoryDataTable.on('draw.dt', function() {
                actionData();
            });
            $('#save-sub-category').click(function() {
                let data = serializeObject($('#form-sub-category'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('sub-category-management.store') }}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-sub-category').modal('hide')
                        window.categoryDataTable.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-sub-category .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-sub-category').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                    }
                });
            });
            $('#update-sub-category').click(function() {
                let data = serializeObject($('#form-sub-category'));
                $.ajax({
                    type: "PATCH",
                    url: `{{ route('sub-category-management.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-sub-category').modal('hide')
                        window.categoryDataTable.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-sub-category .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-sub-category').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                    }
                });
            });
            $('#modal-sub-category').on('hidden.bs.modal', function() {
                window.state = 'add';
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add Sub Category`);
                $('#save-sub-category').removeClass('d-none');
                $('#update-sub-category').addClass('d-none');
                $('#modal-sub-category .is-invalid').removeClass('is-invalid')
                $('#table-sub-category tbody').find('tr').removeClass('selected');
            });
            $('#modal-sub-category').on('show.bs.modal', function() {
                setTimeout(() => {
                    $('.price').inputmask('integer', {
                        radixPoint: ',',
                        groupSeparator: ".",
                        rightAlign: false,
                        allowMinus: false
                    });
                    console.log('taik')
                }, 500);
            });
        });
    </script>
@endsection
