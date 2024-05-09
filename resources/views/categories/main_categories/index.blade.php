@extends('components.index')

@section('title')
主分類管理
@stop

@section('content_header')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1>主分類管理</h1>

@stop

@section('create')
    <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
@stop

@section('data_list')
    @foreach ($datas as $key => $data)
        <tr>
            <td class="align-middle">{{ $key+1 }}</td>
            <td class="align-middle">{{ $data->name }}</td>
            <td class="align-middle">{{ $data->en_name }}</td>
            <td class="align-middle">{!! $data->CategoriesStatusCss !!}</td>
            <td class="align-middle">{{ $data->updated_at }}</td>
            <td class="align-middle">
                {{-- @if(!$data->subCategories->isEmpty()) --}}
                    <a class="btn btn-secondary btn-flat mr-4" href="{{ url()->current() }}/{{ $data->categories_id }}/sub_categories" role="button">查詢子分類</a>
                {{-- @endif --}}
            </td>
            <td class="align-middle">
                @can('main_categories-edit')
                    <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->categories_id}}">編輯</button>
                @endcan
            </td>
        </tr>
    @endforeach
@stop

@section('modal')
    @include('components.formbase_modal')
@stop

@section('css')
@stop

@section('js')
    <script>
        $(document).ready(function() {

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
                    $('#editModalTitle').html('編輯主分類');
                    $.each(data,function(index, child){
                       $('#'+index).val(child);
                    })
                    $('#editId').val(edit_id);
                    $('#en_name').prop('readonly', true);
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
        });
    </script>

    <script>
        $('.reset_search').on('click', function() {
            $("input[name='keywords']").val('');
            $('#search_box').submit();
        })

        function reset_modal(){
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');
            $('#editModalTitle').html('新增主分類');
            $('#editId').val('');
            $('#editForm input[type="text"]').val('');
            $('#en_name').prop('readonly', false);
            $('#editForm textarea').val('');
            $("#editForm select option:first").prop("selected", 'selected');
        }

        function saveAction()
        {
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

