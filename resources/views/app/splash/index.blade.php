@extends('app.splash.base')

@section('title', 'APP-Splash管理')

@section('content_header')
    <h1>APP-Splash管理</h1>
@stop

@can($role_name.'-create')
@section('create')
    <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
@stop
@endcan

@section('data_list')
    @foreach ($datas as $key => $data)
        <tr>
            <td class="align-middle">{{ $data->StatusCss }}</td>
            <td class="align-middle"><img src="{{ $data->android_image }}" style="height: 100px;"></td>
            <td class="align-middle"><img src="{{ $data->iOS_image }}" style="height: 100px;"></td>
            <td class="align-middle">{{ $data->start }}</td>
            <td class="align-middle">{{ $data->end }}</td>
            <td class="align-middle">
                @can('app_splash-edit')
                    <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
                @endcan
            </td>
        </tr>
    @endforeach
@stop

@section('modal')
    @include('app.splash.modal')
    @include('components.gallery_modal')
@stop

@section('css')
@stop

@section('js')

<script>

    // 解決不能使用兩個modal popup問題
    $(document).on('show.bs.modal', '.modal', function(event) {
        $(this).appendTo($('body'));
    }).on('shown.bs.modal', '.modal.in', function(event) {
        setModalsAndBackdropsOrder();
    }).on('hidden.bs.modal', '.modal', function(event) {
        setModalsAndBackdropsOrder();
    });

    // 解決不能使用兩個modal popup問題

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
                $('#editModalTitle').html('編輯APP Splash');
                $('#id').val(edit_id);
                $('#status').val(data.status);
                $('#android_image').val(data.android_image);
                $('#iOS_image').val(data.iOS_image);
                $('#start').val(data.start);
                $('#end').val(data.end);
                if($("#android_image").val()){
                    $("#review_android_image").attr("src",$("#android_image").val());
                    $("#review_android_image_div").show();
                }
                if($("#iOS_image").val()){
                    $("#review_ios_image").attr("src",$("#iOS_image").val());
                    $("#review_ios_image_div").show();
                }
                $('#editModal').modal('show');
            })
        })

        $("#review_android_image_click").click(function(){
            if($("#android_image").val()){
                $("#review_android_image").attr("src",$("#android_image").val());
                $("#review_android_image_div").show();
            }
        });

        $("#review_ios_image_click").click(function(){
            if($("#iOS_image").val()){
                $("#review_ios_image").attr("src",$("#iOS_image").val());
                $("#review_ios_image_div").show();
            }
        });

        $('#gallery_android_image').on('click', function() {
            var parentId = 'android_image';
            $('#android_image').val('');
            $('#galleryModal iframe').attr('data-parent', parentId );
            $('#galleryModal').modal('show');
        });

        $('#gallery_ios_image').on('click', function() {
            var parentId = 'iOS_image';
            $('#iOS_image').val('');
            $('#galleryModal iframe').attr('data-parent', parentId );
            $('#galleryModal').modal('show');
        });

        window.addEventListener("message", (e) => {
            if(e.origin !== 'tvbs.com.tw' || e.origin !== '127.0.0.1') {
                const getMeta = (url, cb) => {
                    const img = new Image();
                    img.onload = () => cb(null, img);
                    img.onerror = (err) => cb(err);
                    img.src = url;
                };
                getMeta(e.data.imgUrl, (err, img) => {
                    if (img.naturalWidth !== 1080 || img.naturalHeight !== 1920) {
                        Swal.fire({
                            title: '圖片尺寸不正確',
                            icon: 'error',
                            confirmButtonText: `確定`,
                            confirmButtonColor: '#f87e6c',
                        });
                    } else {
                        $("#" + e.data.parent).val(e.data.imgUrl);
                    }
                    //console.log(img.naturalWidth, img.naturalHeight);
                });

                $(".gallery-modal-header > .close").trigger('click');
                $("#review_android_image_div").hide();
                $("#review_ios_image_div").hide();
            }
        });

        $('.saveAllBtn').on('click', function() {
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
                    saveAllAction();
                }
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
                    $('#saveBtn').prop('disabled', true);
                    saveAction();
                }
            })

        })
    });

    function reset_modal(){
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');
        $('#editModalTitle').html('編輯APP Splash');
        $('#editId').val('');
        $('#editForm text').val('');
        $('#android_image').val('');
        $('#iOS_image').val('');
        $('#review_android_image_div img').attr('src', '');
        $('#review_ios_image_div img').attr('src', '');
        $('#status').val(0);
        $('#message').attr('rows', '3');
    }

    function saveAction()
    {
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
                });

                $('#saveBtn').prop('disabled', false);
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
