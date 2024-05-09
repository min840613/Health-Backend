@extends('adminlte::page')

@section('title', '科別管理')

@section('content_header')
    <h1>科別管理</h1>
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
                        <button type="button" class="btn btn-outline-info">科別總數
                            <span class="badge">{{$datas->where('type', 1)->count()}}</span>
                        </button>
                    </div>
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-outline-info">上架數
                            <span class="badge">{{$datas->where('type', 1)->where('status', 1)->count()}}</span>
                        </button>
                    </div>
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-outline-info">下架數
                            <span class="badge">{{$datas->where('type', 1)->where('status', 0)->count()}}</span>
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
                <form id="masters_divisions">
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
                                    {!! Form::input('hidden', "masters_divisions[".($key+1)."][id]", $data->id ) !!}
                                </td>
                                <td class="align-middle">
                                    {{ $data->name }}
                                </td>
                                <td class="align-middle">
                                    <img style="width: 30px;" src="{{ $data->icon }}">
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
                                        @if($data->type == 1)
                                            <div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div>
                                        @endif
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

        // 上傳檔案
        const dropbox = $(".upload_zone");

        // 處理選擇完檔案後的動作
        function handleFiles(files, type, dropBoxName, btnName) {

            let putFile = true;
            for (var i = 0; i < files.length; i++) {
                const file = files[i];
                const imageType = /image.*/;
                if (!file.type.match(imageType)) {
                    putFile = false;
                    alert('並非圖片檔案');
                    continue;
                }

                const img = document.createElement("img");
                img.classList.add("preview_image");

                $(dropBoxName).html('');
                $(dropBoxName).append(img);

                const reader = new FileReader();

                reader.onload = function(e) {
                    img.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }

            if (putFile == true) {
                // 將檔案放到file input的動作
                let uploadBtn = document.getElementById(btnName);
                uploadBtn.files = files;
            }
        }

        // 點擊上傳的框框帶入上傳動作
        dropbox.on('click', function(e){
            e.stopPropagation();
            e.preventDefault();
        })

        // 拖曳進入到區塊時加入樣式
        dropbox.on('dragenter', function(e){
            $(this).addClass("upload_zone_enter");
            e.stopPropagation();
            e.preventDefault();
        })

        // 拖曳進入到區塊時移除樣式
        dropbox.on('dragleave', function(e){
            $(this).removeClass("upload_zone_enter");
        })

        // 拖曳在區塊滑來滑去時，也加入樣式
        dropbox.on('dragover', function(e){
            $(this).addClass("upload_zone_enter");
            e.stopPropagation();
            e.preventDefault();
        })

        // 拖曳在區塊，放下檔案時
        dropbox.on('drop', function(e){
            e.stopPropagation();
            e.preventDefault();

            const dt = e.originalEvent.dataTransfer;
            const files = dt.files;
            handleFiles(files, $(this).data('type'), '#'+$(this).attr('id'), $(this).siblings('.file-input').attr('id') );
            $(this).removeClass("upload_zone_enter");
        })
    </script>


    <script>
        $(document).ready(function(){

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
                    $('#editModalTitle').html('編輯科別');

                    $.each(data, function (index, child) {
                        $('#' + index).val(child);
                    })

                    $('#editId').val(edit_id);
                    $("#status").val(data.status).change();

                    const imgSvg = document.createElement("img");
                    imgSvg.classList.add("preview_image");
                    imgSvg.src = data.icon;
                    $("#upload_zone_svg").html('');
                    $("#upload_zone_svg").append(imgSvg);

                    const imgSvgHover = document.createElement("img");
                    imgSvgHover.classList.add("preview_image");
                    imgSvgHover.src = data.icon_hover;
                    $("#upload_zone_svg_hover").html('');
                    $("#upload_zone_svg_hover").append(imgSvgHover);

                    const imgPng = document.createElement("img");
                    imgPng.classList.add("preview_image");
                    imgPng.src = data.icon_android;
                    $("#upload_zone_png").html('');
                    $("#upload_zone_png").append(imgPng);

                    const imgPngHover = document.createElement("img");
                    imgPngHover.classList.add("preview_image");
                    imgPngHover.src = data.icon_android_hover;
                    $("#upload_zone_png_hover").html('');
                    $("#upload_zone_png_hover").append(imgPngHover);

                    if(data.icon_ios != null) {
                        const imgPdf = document.createElement("a");
                        imgPdf.href = data.icon_ios;
                        imgPdf.text = '已上傳圖片（點此下載）';
                        imgPdf.target = '_blank';
                        $("#pdf_content").append(imgPdf);
                    }

                    if(data.icon_ios_hover != null) {
                        const imgPdfHover = document.createElement("a");
                        imgPdfHover.href = data.icon_ios_hover;
                        imgPdfHover.text = '已上傳Hover圖片（點此下載）';
                        imgPdfHover.target = '_blank';
                        $("#pdf_content_hover").append(imgPdfHover);
                    }

                    $('#editModal').modal({
                        backdrop:"static",
                        keyboard:false,
                        show: true
                    });
                })
            })

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

        // 展開modal時，reset裡面的規則
        function reset_modal() {
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');
            $('#editModalTitle').html('新增科別');
            $('#editId').val('');
            $('#editForm input[type="text"]').val('');

            // 已經放到預覽的img都復原
            $('.upload_zone').html('');
            $('.upload_zone').html('<p class="tip">請拖曳圖檔到此</p>');

            // 將file按鈕的有加入檔案的都清除
            document.getElementById('fileUploaderSvg').value = '';
            document.getElementById('fileUploaderSvgHover').value = '';
            document.getElementById('fileUploaderPng').value = '';
            document.getElementById('fileUploaderPngHover').value = '';
            document.getElementById('fileUploaderPdf').value = '';
            document.getElementById('fileUploaderPdfHover').value = '';

            // 移除pdf的下載連結
            $("#pdf_content").html('');
            $("#pdf_content_hover").html('');

        }

        // 儲存排序
        function saveSortAction() {

            let formData = new FormData($('#masters_divisions')[0]);

            formData.append('_method', 'PATCH');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('#masters_divisions input[name="_token"]').val()
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

        // 新增/編輯的儲存動作
        function saveAction() {

            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');

            let formData = new FormData($('#editForm')[0]);

            if ($("#fileUploaderSvg")[0].files[0] != undefined) {
                formData.append("icon", $("#fileUploaderSvg")[0].files[0]);
            }
            if ($("#fileUploaderSvgHover")[0].files[0] != undefined) {
                formData.append("icon_hover", $("#fileUploaderSvgHover")[0].files[0]);
            }

            if ($("#fileUploaderPng")[0].files[0] != undefined) {
                formData.append("icon_android", $("#fileUploaderPng")[0].files[0]);
            }
            if ($("#fileUploaderPngHover")[0].files[0] != undefined) {
                formData.append("icon_android_hover", $("#fileUploaderPngHover")[0].files[0]);
            }

            if ($("#fileUploaderPdf")[0].files[0] != undefined) {
                formData.append("icon_ios", $("#fileUploaderPdf")[0].files[0]);
            }

            if ($("#fileUploaderPdfHover")[0].files[0] != undefined) {
                formData.append("icon_ios_hover", $("#fileUploaderPdfHover")[0].files[0]);
            }

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
