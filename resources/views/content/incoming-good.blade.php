@extends('parent.full')
@section('css')
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row justify-content-evenly mb-3">
                <div class="col">
                    Incoming Good Management
                </div>
                <div class="col text-end">
                    <button class="btn btn-success add-incoming-good" data-bs-target="#modal-incoming-good" data-bs-toggle="modal">Add New Incoming Good</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive mb-3">
                <table id="table-incoming-good" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Action</th>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Recipient</th>
                            <th>Unit</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-incoming-good" tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel4">Add Incoming Good</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-incoming-good" enctype="multipart/form-data">
                        <h3>General Information</h3>
                        @csrf
                        <input type="hidden" name="id">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="operatorId" class="form-label">Operator</label>
                                <select class="form-control" {{ auth()->user()->role == 'Operator' ? 'disabled' : '' }} name="operatorId" id="operatorId">
                                    <option value="">Choose One</option>
                                    @foreach ($operators as $operator)
                                        <option {{ auth()->user()->role == 'Operator' && auth()->user()->id == $operator->id ? 'selected' : '' }}
                                            value="{{ $operator->id }}">{{ $operator->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <label for="categoryId" class="form-label">Category</label>
                                <select class="form-control" name="categoryId" id="categoryId">
                                    <option value="">Choose One</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <label for="subCategoryId" class="form-label">Sub Category</label>
                                <select class="form-control" name="subCategoryId" id="subCategoryId">
                                </select>
                            </div>
                            <div class="col-4 mb-3">
                                <label for="price_limit" class="form-label">Price Limit</label>
                                <input class="form-control total" readonly name="price_limit" id="price_limit" />
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="source" class="form-label">Good Source</label>
                                <input class="form-control" name="source" id="source" />
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <label for="mail_number" class="form-label">Mail number</label>
                                <input class="form-control" name="mail_number" id="mail_number" />
                            </div>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <label for="attachment" class="form-label">Attachment</label>
                                <input type="file"
                                    accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.documen,.zip"
                                    class="form-control" name="attachment" id="attachment" />
                            </div>
                        </div>
                        <h3>Good Information</h3>
                        <div class="row container-input-good gap-2">
                            <div class="col-12 row gap-2 item-input-good">
                                <input type="hidden" name="id[0]" value="">
                                <input type="hidden" name="incomingId[0]" value="">
                                <input type="text" class="form-control col" name="name[0]" placeholder="name">
                                <input type="text" class="form-control col price" name="price[0]" placeholder="price">
                                <input type="text" class="form-control col price" name="amount[0]" placeholder="amount">
                                <input type="text" class="form-control col" name="unit[0]" placeholder="unit">
                                <input type="text" class="form-control col total" name="total[0]" readonly placeholder="total">
                                <input type="date" class="form-control col" name="expired_at[0]">
                                <button type="button" class="btn btn-success btn-icon add-detail-good d-inline col-1 rounded-pill"><i
                                        class="bx bx-plus"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-primary" id="save-incoming-good">Submit</button>
                    <button type="button" class="btn btn-warning d-none" id="update-incoming-good">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/jquery.inputmask.js') }}"></script>
    <script>
        window.incomingGoodDataTable;

        function createElement(data = null) {
            let index = $('.container-input-good').find('.item-input-good').length;
            $('.container-input-good').append(`<div class="col-12 row gap-2 item-input-good">
                                <input type="hidden" name="id[${index}]" value="${data.id??''}">
                                <input type="hidden" name="incomingId[${index}]" value="${data.incomingId??''}">
                                <input type="text" class="form-control col" name="name[${index}]" value="${data.name??''}" placeholder="name">
                                <input type="text" class="form-control col price" name="price[${index}]" value="${data.price??''}" placeholder="price">
                                <input type="text" class="form-control col price" name="amount[${index}]" value="${data.amount??''}" placeholder="amount">
                                <input type="text" class="form-control col" name="unit[${index}]" value="${data.unit??''}" placeholder="unit">
                                <input type="text" class="form-control col total" name="total[${index}]" readonly value="${data.total??''}" placeholder="total">
                                <input type="date" class="form-control col" name="expired_at[${index}]" value="${data.expired_at??''}">
                                <button type="button" class="btn btn-danger btn-icon remove-detail-good d-inline col-1 rounded-pill"><i
                                        class="bx bx-x"></i></button>
                            </div>`);
            $('.remove-detail-good').click(function() {
                $(this).parents('.item-input-good').remove();
            });
            $('.price').inputmask('integer', {
                radixPoint: ',',
                groupSeparator: ".",
                rightAlign: false,
                allowMinus: false
            });
            $('.total').inputmask('integer', {
                radixPoint: ',',
                groupSeparator: ".",
                rightAlign: false,
                allowMinus: false
            });
            $('.price').keyup(function() {
                countPrice();
            })
        }

        function actionData() {
            $('.edit').click(function() {
                window.state = 'update';
                let idData = $(this).data("incoming");
                $("#update-incoming-good").data("incoming", idData);
                if (window.incomingGoodDataTable.rows('.selected').data().length == 0) {
                    $('#table-incoming-good tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }

                var data = window.incomingGoodDataTable.rows('.selected').data()[0];

                $('#modal-incoming-good').modal('show');
                $('#modal-incoming-good').find('.modal-title').html(`Edit Incoming Good`);
                $('#save-incoming-good').addClass('d-none');
                $('#update-incoming-good').removeClass('d-none');

                $.ajax({
                    type: "GET",
                    url: "{{ route('incoming-good.show') }}/" + idData,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-incoming-good').find("form")
                            .find('input, select').map(function(index, element) {
                                if (response.data[element.name] && element.name != 'attachment' && element.name !==
                                    'subCategoryId') {
                                    $(`[name=${element.name}]`).val(response.data[element
                                        .name]).trigger('change');
                                }
                            });
                        setTimeout(() => {
                            $(`[name=subCategoryId]`).val(response.data['subCategoryId']).trigger('change');
                        }, 3000);
                        response.data.details.forEach((data, index) => {
                            if (index == 0) {
                                $('.item-input-good:first').find('input').map((index, input) => {
                                    let arrayKey = Object.keys(data);
                                    if (arrayKey.includes($(input).attr('name').split('[0]').join(''))) {
                                        $(input).val(data[$(input).attr('name').split('[0]').join('')]).trigger('change')
                                    }
                                })
                            } else {
                                createElement(data)
                            }
                        })
                    },
                    error: function(error) {}
                });
            });
            $('.delete').click(function() {
                if (window.incomingGoodDataTable.rows('.selected').data().length == 0) {
                    $('#table-event tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idData = $(this).data("incoming");
                var data = window.incomingGoodDataTable.rows('.selected').data()[0];
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('incoming-good.destroy') }}/" +
                        idData,
                    data: {
                        _token: `{{ csrf_token() }}`,
                    },
                    dataType: "json",
                    success: function(response) {
                        window.incomingGoodDataTable.ajax.reload();
                    },
                    error: function(error) {}
                });
            });
            $('.status-update').click(function() {
                if (window.incomingGoodDataTable.rows('.selected').data().length == 0) {
                    $('#table-event tbody').find('tr').removeClass('selected');
                    $(this).parents('tr').addClass('selected')
                }
                let idData = $(this).data("incoming");
                var data = window.incomingGoodDataTable.rows('.selected').data()[0];
                $.ajax({
                    type: "PATCH",
                    url: "{{ route('incoming-good.update-status') }}/" +
                        idData,
                    data: {
                        _token: `{{ csrf_token() }}`,
                    },
                    dataType: "json",
                    success: function(response) {
                        window.incomingGoodDataTable.ajax.reload();
                    },
                    error: function(error) {}
                });
            });
        }

        function countPrice() {
            $('.item-input-good').map(function(index, element) {
                let valid = true
                $(element).find('.price').map(function(index, input) {
                    if ($(input).val() == '') {
                        valid = false;
                    }
                })
                if (valid) {
                    let price = parseInt($(element).find('.price:first').val().split('.').join(''));
                    let amount = parseInt($(element).find('.price:last').val().split('.').join(''));
                    let price_limit = parseInt($('#price_limit').val().split('.').join(''));
                    if (price >= price_limit) {
                        $(element).find('input[readonly]').val(price * amount)
                    }
                }
            })
        }
        $(function() {
            window.incomingGoodDataTable = $('#table-incoming-good').DataTable({
                ajax: "{{ route('incoming-good.data-table') }}",
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
                    name: 'created_at',
                    data: 'created_at',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data.split('T').join(' ').split('.000000Z').join('')}</div>`
                    }
                }, {
                    target: 3,
                    name: 'source',
                    data: 'source',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 4,
                    name: 'operator_name',
                    data: 'operator_name',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 5,
                    name: 'unit',
                    data: 'unit',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 6,
                    name: 'code',
                    data: 'code',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 7,
                    name: 'name',
                    data: 'name',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 8,
                    name: 'price',
                    data: 'price',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 9,
                    name: 'amount',
                    data: 'amount',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 10,
                    name: 'total',
                    data: 'total',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }, {
                    target: 11,
                    name: 'status',
                    data: 'status',
                    orderable: true,
                    searchable: true,
                    render: (data, type, row, meta) => {
                        return `<div class='text-wrap'>${data}</div>`
                    }
                }]
            });
            window.incomingGoodDataTable.on('draw.dt', function() {
                actionData();
                let columnIndex = 2;
                let mergeIndex = [0, 1, 2, 3, 4, 5];
                let lastCells = {};
                let lastValues = {};
                mergeIndex.forEach(index => {
                    lastCells[index] = null;
                    lastValues[index] = null;
                });
                $('#table-incoming-good tbody tr').each(function() {
                    let referenceCell = $(this).find("td").eq(columnIndex);
                    let referenceValue = referenceCell.text().trim();
                    if (referenceValue === lastValues[columnIndex] && referenceValue !== '') {
                        mergeIndex.forEach(index => {
                            let cell = $(this).find("td").eq(index);
                            cell.hide();
                            lastCells[index].attr("rowspan", parseInt(lastCells[index].attr("rowspan") || 1) + 1);
                        });
                    } else {
                        lastValues[columnIndex] = referenceValue;
                        mergeIndex.forEach(index => {
                            lastCells[index] = $(this).find("td").eq(index);
                        });
                    }
                });
            });
            $('#categoryId').change(function() {
                $.ajax({
                    type: "GET",
                    url: `{{ route('sub-category-management.all') }}/${this.value}`,
                    dataType: "json",
                    success: function(response) {
                        let html = "<option value=''>Choose One</option>";
                        response.data.forEach(data => {
                            html += `<option value="${data.id ? data.id : data.name}">${data.name}</option>`;
                        });
                        $('#subCategoryId').html(html);
                    }
                });
            });
            $('#subCategoryId').change(function() {
                $.ajax({
                    type: "GET",
                    url: `{{ route('sub-category-management.show') }}/${this.value}`,
                    dataType: "json",
                    success: function(response) {
                        $('[name=price_limit]').val(response.data.price_limit)
                    }
                });
            })
            $('.add-detail-good').click(function() {
                createElement();
            })
            $('#save-incoming-good').click(function() {
                $('#form-incoming-good').find('[disabled]').removeAttr('disabled')
                let data = serializeFiles(document.getElementById('form-incoming-good'));
                $.ajax({
                    type: "POST",
                    url: `{{ route('incoming-good.store') }}`,
                    data: data,
                    dataType: "json",
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#modal-incoming-good').modal('hide')
                        window.incomingGoodDataTable.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-incoming-good .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-incoming-good').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                    }
                });
            });
            $('#update-incoming-good').click(function() {
                let data = serializeObject($('#form-incoming-good'));
                $.ajax({
                    type: "PATCH",
                    url: `{{ route('incoming-good.update') }}/${data.id}`,
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        $('#modal-incoming-good').modal('hide')
                        window.incomingGoodDataTable.ajax.reload();
                    },
                    error: function(error) {
                        $('#modal-incoming-good .is-invalid').removeClass('is-invalid')
                        $.each(error.responseJSON.errors, function(indexInArray,
                            valueOfElement) {
                            $('#modal-incoming-good').find('[name=' + indexInArray +
                                ']').addClass('is-invalid')
                        });
                    }
                });
            });
            $('#modal-incoming-good').on('hidden.bs.modal', function() {
                window.state = 'add';
                $(this).find('form')[0].reset();
                $(this).find('.modal-title').html(`Add Incoming Good`);
                $('#save-incoming-good').removeClass('d-none');
                $('#update-incoming-good').addClass('d-none');
                $('#modal-incoming-good .is-invalid').removeClass('is-invalid')
                $('#table-incoming-good tbody').find('tr').removeClass('selected');
            });
            $('.price').keyup(function() {
                countPrice();
            })
            $('#modal-incoming-good').on('show.bs.modal', function() {
                setTimeout(() => {
                    $('.price').inputmask('integer', {
                        radixPoint: ',',
                        groupSeparator: ".",
                        rightAlign: false,
                        allowMinus: false
                    });
                    $('.total').inputmask('integer', {
                        radixPoint: ',',
                        groupSeparator: ".",
                        rightAlign: false,
                        allowMinus: false
                    });
                }, 500);
            });
        });
    </script>
@endsection
