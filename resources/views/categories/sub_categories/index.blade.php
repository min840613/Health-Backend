@extends('components.index')

@section('title')
子分類管理：{{isset($main_categories->name) ? $main_categories->name: ''}}
@stop

@section('content_header')
    <h1>子分類管理：{{isset($main_categories->name) ? $main_categories->name: ''}}</h1>
@stop

@section('create')
    <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
    <button type="button" class="btn btn-block btn-primary btn-flat float-right px-4 saveSortBtn">儲存排序</button>
@stop

@section('otherBtn')
    <a href="{{route('main_categories.index')}}">
        <button type="button" class="btn btn-block btn-danger btn-flat float-right px-4">回主分類</button>
    </a>
@stop

@section('data_list')
    @foreach ($datas as $key => $data)
        <tr sid="{{$data->sub_categories_id}}" data-id="{{$key+1}}" data-rowid="{{$data->sub_categories_id}}">
            <td class="align-middle">{{ $key+1 }}</td>
            <td class="align-middle">{{ $data->name }}</td>
            <td class="align-middle">{{ $data->updated_at }}</td>
            <td class="align-middle">{{ $data->status }}</td>
            <td class="align-middle">
                @can('main_categories-edit')
                    <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->sub_categories_id}}">編輯</button>
                @endcan
            </td>
            <td class="align-middle"><div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div></td>
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
        $(".table tbody").sortable({
            handle: ".handle",
            stop: function(event, ui ){
            }
        });

        // 儲存排序
        $('.saveSortBtn').on('click', function() {
            var sortedIDs = $( ".table tbody" ).sortable( "toArray", {  attribute: "sid"});

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            });

            $.ajax({
                data: JSON.stringify({"ids":sortedIDs}),
                url: '{{route("sub_categories.sort")}}',
                type: 'PUT',
                dataType: 'JSON',
                cache: false,
                processData: false,
                contentType: "application/json",
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
                    Swal.fire({
                        title: '儲存失敗',
                        icon: 'error',
                        confirmButtonText: `確定`,
                        confirmButtonColor: '#f87e6c',
                    })
                }
            });
        });

    </script>
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
                    $('#editModalTitle').html('編輯子分類');
                    $.each(data,function(index, child){
                       $('#'+index).val(child);
                    })
                    $("#status").val(data.status).change();
                    $('#editId').val(edit_id);
                    if($('#en_name').val() != ''){
                        $('#en_name').prop('readonly', true);
                    }
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
            $('#editModalTitle').html('新增子分類');
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
                    validateMsg += '<li>' + v + '</li>';
                });
            });

            validateMsg += '</ul>'

            $('#validateMsg').html(validateMsg);
            $('#validateMsg').css('display', 'block');
        }

    </script>
@stop

