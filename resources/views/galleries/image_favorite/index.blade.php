@extends('galleries.image_favorite.base')

@section('title', '常用圖片管理')

@section('content_header')
    <h1>常用圖片管理</h1>
@stop

@section('create')
    <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
@stop

@section('data_list')
    @foreach ($datas as $key => $data)
        <tr>
            <td class="align-middle">{{ $key+1 }}</td>
            <td class="align-middle">{{ $data->title }}</td>
            <td class="align-middle"><img src="{{ $data->url }}" style="height: 100px;"></td>
            <td class="align-middle">
            @can('image_favorite-edit')
                <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
            @endcan
            </td>
            <td class="align-middle">
            @can('image_favorite-delete')
                <button type="button" class="btn btn-danger btn-flat mr-4 deleteBtn" data-id="{{$data->id}}">刪除</button>
            @endcan
            </td>
        </tr>
    @endforeach
@stop

@section('modal')
    @include('galleries.image_favorite.modal')
    @include('components.gallery_modal')
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
                $('#editModalTitle').html('編輯常用圖片');
                $('#id').val(edit_id);
                $.each(data,function(index, child){
                    if(index == 'content'){
                        tinymce.get('content').setContent(child);
                    }else{
                        $('#'+index).val(child);
                    }
                })
                if($("#url").val()){
                    $("#review_image").attr("src",$("#url").val());
                    $("#review_image_div").show();
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

        $("#review_image_click").click(function(){
            if($("#url").val()){
                $("#review_image").attr("src",$("#url").val());
                $("#review_image_div").show();
            }
        })

        $('#gallery_image').on('click', function() {
            var parentId = 'url';
            $('#galleryModal iframe').attr('data-parent', parentId );
            $('#galleryModal').modal({backdrop: 'static', keyboard: false}, 'show');
        })

        window.addEventListener("message", (e) => {
            if(e.origin !== 'tvbs.com.tw' || e.origin !== '127.0.0.1') {
                $("#" + e.data.parent).val(e.data.imgUrl);
                $(".gallery-modal-header > .close").trigger('click');
                $("#review_image_div").hide();
            }
        });
    });


    function reset_modal(){
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');
        $('#editModalTitle').html('新增常用圖片');
        $('#editId').val('');
        $('#editForm input[type="text"]').val('');
        $('#review_image_div img').attr('src', '');
    }


    function saveAction()
    {
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');

        let formData = new FormData($('#editForm')[0]);

        let ajaxUrl = '';
        let ajaxMethod = '';

        if($('#id').val() == ''){
            ajaxUrl = "{{url()->current()}}";
        }else{
            ajaxUrl = "{{url()->current()}}/"+$('#id').val();
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
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
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
