@extends('components.index')

@section('title')
Line文章供稿 - 條件管理
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

    <h1>Line文章供稿 - 條件管理</h1>

@stop

@section('create')
    @can($role_name.'-create')
        @can($role_name.'-edit')
            <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 manageBtn">管理</button>
        @endcan
    @endcan
@stop

@section('data_list')
    @foreach ($datas as $key => $data)
        <tr>
            <td class="align-middle">{{ $data->category_id }}</td>
            <td class="align-middle">{{ $data->category_en_name }}</td>
            <td class="align-middle">{{ $data->category_name }}</td>
            <td class="align-middle">{{ $data->updated_user }}</td>
            <td class="align-middle"> - </td>
        </tr>
    @endforeach
@stop

<!-- Modal Start-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="demoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <div class="modal-body">
                <div id="validateMsg" class="alert alert-danger" style="display: none;">
                </div>
                <form id="editForm" name="editForm" class="form-horizontal" onsubmit="return false;">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="categories">允許Line供稿之分類(多選)</label>
                        {!! Form::select('categories[]', $categories, null, array('class' => 'form-control','multiple', 'size' => 18, 'id' => 'categories')) !!}
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                <button id="saveBtn" type="button" class="btn btn-primary">儲存</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal End-->



@section('css')
@stop

@section('js')
    <script>
        $(document).ready(function() {
            //  Model hide 事件
            $('#editModal').on('hidden.bs.modal', function () {
                reset_modal();
            })

            $('.manageBtn').on('click', function() {
                reset_modal();
                $('#editModal').modal('show');
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
        function reset_modal(){
            var selectCategories = {{$datas->pluck('category_id')}};
            $('#categories').val(selectCategories);
        }

        function saveAction()
        {
            let formData = new FormData($('#editForm')[0]);

            let ajaxUrl = '{{route("line_articles.condition.save")}}';

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

