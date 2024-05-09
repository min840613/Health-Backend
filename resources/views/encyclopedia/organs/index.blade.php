@extends('adminlte::page')

@section('title', '器官與組織')

@section('content_header')
    <h1>器官與組織</h1>

    {{ Form::open(['route' => 'organs.index', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    <div class="row col-6">
        <div class="float-left mr-5 col-4">
            <label>選取身體部位</label>
            <x-adminlte-select2 name="body_id" igroup-size="sm">
                @foreach($filters['bodies'] as $body)
                    <option
                        value="{{$body['id']}}" {{request()->input('body_id') == $body['id'] ? 'selected' : ''}}>{{$body['name']}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
<!--         <div class="float-left mr-3">
            <x-button type="submit" name="查詢" />
        </div> -->
    </div>
    {!! Form::close() !!}
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @if(!isset($create) || $create == true)
                    @can($role_name.'-create')
                        <div class="row">
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
                <form id="encyclopedia_organs">
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
                        @foreach ($datas->organs as $key => $data)
                            <tr data-id="{{$key+1}}">
                                <td class="align-middle">
                                    {{ $key+1 }}
                                    {!! Form::input('hidden', "encyclopedia_organs[".($key+1)."][id]", $data->id ) !!}
                                </td>
                                <td class="align-middle">
                                    {{ $datas->name }}
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
                                        <a class="btn btn-secondary btn-flat mr-4" href="{{route('sickness.index', ['body_id' => $data->body_id, 'organ_id' => $data->id])}}" role="button">點我看疾病子分類</a>
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
        
        // 上傳檔案
        const dropbox = $(".upload_zone");

        // 處理選擇完檔案後的動作
        function handleFiles(files, type) {

            let btnName = '';
            let dropBoxName = '';
            if (type == 'svg') {
                dropBoxName = '#upload_zone_svg';
                btnName = 'fileUploaderSvg';
            } else if (type == 'png') {
                dropBoxName = '#upload_zone_png';
                btnName = 'fileUploaderPng';
            }

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
            handleFiles(files, $(this).data('type'));
            $(this).removeClass("upload_zone_enter");
        })
    </script>


    <script>
        $(document).ready(function(){

            // 選擇完身體的選項後，導轉到指定的body_id參數
            $('#body_id').change(function(){
                window.location.href = '{{url()->current()}}' + '?body_id=' + $('#body_id').val();
            })
         
            // 設定modal的寬度
            $('.modal-dialog').width('100%');
            $('.modal-dialog').css('max-width', '45%');

            //  Model hide 事件
            // $('#editModal').on('hidden.bs.modal', function () {
            //     reset_modal();
            // })

            // 點擊新增的按鈕
            $('.createBtn').on('click', function () {
                reset_modal();
                
                $('#editModal').modal('show');
                $('#editModal').modal('handleUpdate');
            })

            // 點擊編輯的按鈕
            $('.editBtn').on('click', function () {
                var edit_id = $(this).data('id');
                var editUrl = "{{url()->current()}}/" + edit_id + '/edit';
                $.get(editUrl, function (data) {
                    reset_modal();
                    $('#editModalTitle').html('編輯醫學百科-器官與組織');

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

                    const imgPng = document.createElement("img");
                    imgPng.classList.add("preview_image");
                    imgPng.src = data.icon_android; 
                    $("#upload_zone_png").html('');
                    $("#upload_zone_png").append(imgPng);

                    const imgPdf = document.createElement("a");
                    imgPdf.href = data.icon_ios; 
                    imgPdf.text = '已上傳圖片（點此下載）'; 
                    imgPdf.target = '_blank'; 
                    $("#pdf_content").append(imgPdf);

                    $('#editModal').modal('show');
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
            $('#editModalTitle').html('新增醫學百科-器官與組織');
            $('#editId').val('');
            $('#editForm input[type="text"]').val('');
            
            // 已經放到預覽的img都復原
            $('.upload_zone').html('');
            $('.upload_zone').html('<p class="tip">請拖曳圖檔到此</p>');

            // 將file按鈕的有加入檔案的都清除
            document.getElementById('fileUploaderSvg').value = '';
            document.getElementById('fileUploaderPng').value = '';
            document.getElementById('fileUploaderPdf').value = '';

            // 移除pdf的下載連結
            $("#pdf_content").html('');
            
        }

        // 儲存排序
        function saveSortAction() {

            let formData = new FormData($('#encyclopedia_organs')[0]);

            formData.append('_method', 'PATCH');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('#encyclopedia_organs input[name="_token"]').val()
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
            if ($("#fileUploaderPng")[0].files[0] != undefined) {
                formData.append("icon_android", $("#fileUploaderPng")[0].files[0]);
            }
            if ($("#fileUploaderPdf")[0].files[0] != undefined) {
                formData.append("icon_ios", $("#fileUploaderPdf")[0].files[0]);
            }


            // formData.append("icon", $("#fileUploaderSvg")[0].files[0]);
            // formData.append("icon_android", $("#fileUploaderPng")[0].files[0]);
            // formData.append("icon_ios", $("#fileUploaderPdf")[0].files[0]);
            formData.append("body_id", $('#body_id').val());

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
                    console.log(err);
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
