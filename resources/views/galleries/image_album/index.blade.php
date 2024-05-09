@extends('adminlte::page')

@section('title', '相簿管理')

@section('content_header')
    <h1>相簿管理</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @if(!isset($create) || $create == true)
                @can($role_name.'-create')
                <div class="float-left mr-3">
                    <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
                </div>
                @endcan
                @endif
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                <form id="image_album">
                    {{ csrf_field() }}
                    <table id="sort_table" class="table sort_table">
                        <thead>
                            <tr>
                                @if(!isset($no_id) || $no_id == false)
                                <th style="width: 10px">ID</th>
                                @endif
                                @foreach ($field as $value)
                                    <th>{{ $value }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $key => $data)
                                <tr data-id="{{$key+1}}">
                                    <td class="align-middle">
                                        {{ $data->id }}
                                        {!! Form::input('hidden', "image_album[".($key+1)."][id]", $data->id ) !!}
                                    </td>
                                    <td class="align-middle" style="width:25%;">
                                        <?= $data->title ?>
                                    </td>
                                    <td class="align-middle">
                                        @can('image_album-edit')
                                            <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
                                        @endcan
                                    </td>
                                    <td class="align-middle">
                                        @can('image_album-delete')
                                            <button type="button" class="btn btn-danger btn-flat mr-4 deleteBtn" data-id="{{$data->id}}">刪除</button>
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
    @yield('js')
@stop

@section('js')
<script>
    $(document).ready(function() {

        $('.modal-dialog').width('70%');
        $('.modal-dialog').css('max-width', '80%');

        //  Model hide 事件
        $('#editModal').on('hidden.bs.modal', function () {
            reset_modal();
        })

        $('.createBtn').on('click', function() {
            reset_modal();
            $('#editModal').modal('show');
        })

        $('.editBtn').on('click', function() {
            var edit_id = $(this).data('id');
            var editUrl = "{{url()->current()}}/"+ edit_id + '/edit';
            $.get(editUrl, function (data) {
                reset_modal();
                $('#editModalTitle').html('編輯相簿');
                $.each(data,function(index, child){
                    if(index == 'content'){
                        tinymce.get('content').setContent(child);
                    }else{
                        $('#'+index).val(child);
                    }
                })
                $('#editId').val(edit_id);

                $('#editModal').modal('show');
            })
        })

        $('#saveBtn').on('click', function() {
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
                    saveAction();
                }
            })

        })

        $('.deleteBtn').on('click', function() {
            Swal.fire({
                title: '確定刪除？',
                icon: 'question',
                iconColor: '#f87e6c',
                showDenyButton: true,
                confirmButtonText: `確定`,
                confirmButtonColor: '#f87e6c',
                denyButtonText: `取消`,
                denyButtonColor: '#9c9996'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteAction($(this).attr('data-id'));
                }
            })

        })
    });


    function reset_modal(){
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');
        $('#editModalTitle').html('新增相簿');
        $('#editId').val('');
        $('#editForm input[type="text"]').val('');
    }


    function saveAction()
    {
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');

        let formData = new FormData($('#editForm')[0]);

        let ajaxUrl = '';
        let ajaxMethod = '';

        if($('#editId').val() == ''){
            ajaxUrl = "{{url()->current()}}";
        }else{
            ajaxUrl = "{{url()->current()}}/"+$('#editId').val();
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
                console.log(err);
                if (err.status == 422) {
                    printErrorMessage(err.responseJSON.errors);
                }

                if(err.status == 400 && err.responseJSON.errMsg != undefined){
                    Swal.fire({
                        title: err.responseJSON.errMsg,
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }else{
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

    function deleteAction(text_id)
    {
        let deleteFormData = new FormData();
        deleteFormData.append('_method', 'DELETE');


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#image_album input[name="_token"]').val()
            }
        });

        $.ajax({
            data: deleteFormData,
            url: "{{url()->current()}}/"+text_id,
            type: 'POST',
            dataType: 'JSON',
            cache: false,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data);
                Swal.fire({
                    title: '刪除成功',
                    icon: 'success',
                    confirmButtonText: `確定`,
                    confirmButtonColor: '#f87e6c',
                }).then((result) => {
                    window.location.reload();
                })
            },
            error: function (err) {
                console.log(err);
                if (err.status == 422) {
                    printErrorMessage(err.responseJSON.errors);
                }

                if(err.status == 400 && err.responseJSON.errMsg != undefined){
                    Swal.fire({
                        title: err.responseJSON.errMsg,
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }else{
                    Swal.fire({
                        title: '刪除失敗',
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }
            }
        });
    }

    function printErrorMessage(errorMessage){

        let validateMsg = '<ul class="mb-0">';

        $.each(errorMessage, function(key, value){
            $.each(value, function(k, v){
                validateMsg += '<li>' + value + '</li>';
            });
        });

        validateMsg += '</ul>'

        $('#validateMsg').html(validateMsg);
        $('#validateMsg').css('display', 'block');
    }
</script>
@stop
