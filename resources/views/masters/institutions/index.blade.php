@extends('adminlte::page')

@section('title', '醫療院所')

@section('content_header')
    <h1>醫療院所</h1>
    {{ Form::open(['route' => 'institutions.index', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    <div class="row col-6">
        <div class="col-4">
            <label>院所模糊搜尋</label>
            <x-adminlte-input name="filter_name" igroup-size="sm"
                              value="{{request()->input('filter_name')}}"/>
        </div>
    </div>
    <div class="row col-6">
        <div class="col-2">
            <x-button type="submit" name="查詢"/>
        </div>
        <div class="col-4">
            <x-button type="button" name="取消查詢" addClass="reset"/>
        </div>
    </div>
    {{ Form::close() }}
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    @if(!isset($create) || $create == true)
                        @can($role_name.'-create')
                            <div class="float-left mr-3">
                                <button type="button"
                                        class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增
                                </button>
                            </div>
                            <div class="float-left mr-3">
                                <button type="button"
                                        class="btn btn-block btn-danger btn-flat float-right px-4 saveSortBtn">儲存排序
                                </button>
                            </div>
                        @endcan
                    @endif
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-outline-info">院所總數
                            <span class="badge">{{$datas->count()}}</span>
                        </button>
                    </div>
                        <div class="float-left mr-3">
                            <button type="button" class="btn btn-outline-info">醫學中心數
                                <span class="badge">{{$datas->where('is_centre', 1)->count()}}</span>
                            </button>
                        </div>
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-outline-info">上架數
                            <span class="badge">{{$datas->where('status', 1)->count()}}</span>
                        </button>
                    </div>
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-outline-info">下架數
                            <span class="badge">{{$datas->where('status', 0)->count()}}</span>
                        </button>
                    </div>
                </div>
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                <form id="masters_institutions">
                    {{ csrf_field() }}
                    <table id="sort_table" class="table sort_table">
                        <thead>
                        <tr>
                            @foreach ($field as $value)
                                <th>{{ $value }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($datas as $key => $data)
                            <tr data-id="{{$key+1}}">
                                <td class="align-middle">
                                    {{ $key+1 }}
                                    {!! Form::input('hidden', "masters_institutions[".($key+1)."][id]", $data->id ) !!}
                                </td>
                                <td class="align-middle">
                                    {{ $data->name }}
                                </td>
                                <td class="align-middle">
                                    {{ $data->nick_name}}
                                </td>
                                <td class="align-middle">
                                    {{ $data->created_at->toDateTimeString() }}
                                </td>
                                <td class="align-middle">
                                    {{ $data->updated_at->toDateTimeString() }}
                                </td>
                                <td class="align-middle">
                                    @if($data->status == 1)
                                        <i style="color: green;" class="fa fa-check"></i>
                                    @else
                                        <i style="color: #b80000;" class="fa fa-times"></i>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @can($role_name.'-edit')
                                        <button type="button" class="btn btn-primary btn-flat mr-4 editBtn"
                                                data-id="{{$data->id}}">編輯
                                        </button>
                                    @endcan
                                </td>
                                <td class="align-middle">
                                    @can($role_name.'-edit')
                                        <div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
    @include('components.formbase_modal')
@stop

@section('css')
@stop

@section('js')
    <script>
        //排序功能
        $(".sort_table tbody").sortable({handle: ".handle"});

        $(document).ready(function () {
            // 取消查詢
            $('.reset').click(function () {
                location.href = "{{route('institutions.index')}}"
            });

            // 設定modal的寬度
            $('.modal-dialog').width('100%');
            $('.modal-dialog').css('max-width', '45%');

            // 點擊新增的按鈕
            $('.createBtn').on('click', function () {
                reset_modal();

                $('#editModal').modal({
                    backdrop:"static",
                    keyboard:false,
                    show: true
                });
                $('#editModal').modal('handleUpdate');
            })

            // 點擊編輯的按鈕
            $('.editBtn').on('click', function () {
                var edit_id = $(this).data('id');
                var editUrl = "{{url()->current()}}/" + edit_id + '/edit';
                $.get(editUrl, function (data) {
                    reset_modal();
                    $('#editModalTitle').html('編輯醫療院所');

                    $.each(data, function (index, child) {
                        if (index === 'is_centre') {
                            if(child == 1){
                                $('#is_centre').prop('checked', 'checked')
                            }
                        } else {
                            $('#' + index).val(child);
                        }
                    })

                    $('#editId').val(edit_id);
                    $("#status").val(data.status).change();

                    $('#editModal').modal({
                        backdrop:"static",
                        keyboard:false,
                        show: true
                    });
                })
            })

            $('#saveBtn').on('click', function () {
                Swal.fire({
                    title: '確定儲存？',
                    icon: 'question',
                    iconColor: '#f87e6c',
                    showDenyButton: true,
                    confirmButtonText: `確定`,
                    confirmButtonColor: '#f87e6c',
                    denyButtonText: `取消`,
                    denyButtonColor: '#9c9996'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#saveBtn').prop('disabled', true);
                        saveAction();
                    }
                })

            })
        });

        // 儲存排序
        $('.saveSortBtn').on('click', function () {
            Swal.fire({
                title: '確定儲存？',
                icon: 'question',
                iconColor: '#f87e6c',
                showDenyButton: true,
                confirmButtonText: `確定`,
                confirmButtonColor: '#f87e6c',
                denyButtonText: `取消`,
                denyButtonColor: '#9c9996'
            }).then((result) => {
                if (result.isConfirmed) {
                    saveSortAction();
                }
            })
        })

        // 儲存排序
        function saveSortAction() {

            let formData = new FormData($('#masters_institutions')[0]);

            formData.append('_method', 'PATCH');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('#masters_institutions input[name="_token"]').val()
                }
            });

            $.ajax({
                data: formData,
                url: "{{url()->current()}}/" + 'sort',
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    Swal.fire({
                        title: '儲存成功',
                        icon: 'success',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    }).then((result) => {
                        window.location.reload();
                    })
                },
                error: function (err) {
                    if (err.status == 422) {
                        printErrorMessage(err.responseJSON.errors);
                    }

                    if (err.status == 400 && err.responseJSON.errMsg != undefined) {
                        Swal.fire({
                            title: err.responseJSON.errMsg,
                            icon: 'error',
                            confirmButtonText: `確定`,
                            confirmButtonColor: '#f87e6c',
                        })
                    } else {
                        Swal.fire({
                            title: '儲存失敗',
                            icon: 'error',
                            confirmButtonText: `確定`,
                            confirmButtonColor: '#f87e6c',
                        })
                    }
                }
            });
        }

        // 展開modal時，reset裡面的規則
        function reset_modal() {
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');
            $('#editModalTitle').html('新增醫療院所');
            $('#editId').val('');
            $('#editForm input[type="text"]').val('');
        }

        // 新增/編輯的儲存動作
        function saveAction() {

            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');

            let formData = new FormData($('#editForm')[0]);

            let ajaxUrl = '';

            if ($('#editId').val() == '') {
                ajaxUrl = "{{url()->current()}}";
            } else {
                ajaxUrl = "{{url()->current()}}/" + $('#editId').val();
                formData.append('_method', 'PATCH');
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });

            $.ajax({
                data: formData,
                url: ajaxUrl,
                type: 'POST',
                dataType: 'JSON',
                cache: false,
                processData: false,
                contentType: false,
                success: function (data) {
                    console.log(data);
                    Swal.fire({
                        title: '儲存成功',
                        icon: 'success',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    }).then((result) => {
                        window.location.reload();
                    })
                },
                error: function (err) {
                    $('#saveBtn').prop('disabled', false);
                    if (err.status == 422) {
                        printErrorMessage(err.responseJSON.errors);
                    }

                    if (err.status == 400 && err.responseJSON.errMsg != undefined) {
                        Swal.fire({
                            title: err.responseJSON.errMsg,
                            icon: 'error',
                            confirmButtonText: `確定`,
                            confirmButtonColor: '#f87e6c',
                        })
                    } else {
                        Swal.fire({
                            title: '儲存失敗',
                            icon: 'error',
                            confirmButtonText: `確定`,
                            confirmButtonColor: '#f87e6c',
                        })
                    }
                }
            });
        }

        function printErrorMessage(errorMessage) {
            console.log(errorMessage);
            let validateMsg = '<ul class="mb-0">';

            $.each(errorMessage, function (key, value) {
                $.each(value, function (k, v) {
                    validateMsg += '<li>' + v + '</li>';
                    return false;
                });
                return false;
            });

            validateMsg += '</ul>'

            $('#validateMsg').html(validateMsg);
            $('#validateMsg').css('display', 'block');
        }
    </script>
@stop
