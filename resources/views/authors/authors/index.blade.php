@extends('components.index')

@section('title')
上稿者管理
@stop

@section('content_header')
    <h1>上稿者管理</h1>
@stop

@section('create')
    <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
@stop

@section('data_list')
    @foreach ($datas as $key => $data)
        <tr>
            <td class="align-middle">{{ $key+1 }}</td>
            <td class="align-middle">{{ $data->name }}</td>
            <td class="align-middle">{!! $data->StatusCss !!}</td>
            <td class="align-middle">
                @can('authors-edit')
                    <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
                @endcan
            </td>
        </tr>
    @endforeach
@stop

@section('modal')
    @include('authors.authors.modal')
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
                    $('#editModalTitle').html('編輯上稿者');
                    $('#author_id').val(edit_id);
                    $('#name').val(data.name);
                    $('#status').val(data.status);
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
            $('#editModalTitle').html('新增上稿者');
            $('#author_id').val('');
            $('#name').val('');
            $('#status').val(1);
        }

        function saveAction()
        {
            let formData = new FormData($('#editForm')[0]);

            let ajaxUrl = '';
            let ajaxMethod = '';

            if($('#author_id').val() == ''){
                ajaxUrl = "{{url()->current()}}";
            }else{
                ajaxUrl = "{{url()->current()}}/"+$('#author_id').val();
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
                    if (err.status == 422) {
                        printErrorMessage(err.responseJSON.errors);
                    }

                    Swal.fire({
                        title: '儲存失敗',
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
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

