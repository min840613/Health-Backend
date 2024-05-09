@extends('adminlte::page')

@section('title', '疾病管理')

@section('content_header')
    <h1>疾病管理</h1>

    {{ Form::open(['route' => 'sickness.index', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}
    <div class="row col-6">
        <div class="float-left mr-3">
            <label>目前身體部位</label>
            <x-adminlte-select2 name="body_id" igroup-size="sm">
                @foreach($filters['bodies'] as $body)
                    <option
                        value="{{$body['id']}}" {{request()->input('body_id') == $body['id'] ? 'selected' : ''}}>{{$body['name']}}</option>
                @endforeach
            </x-adminlte-select2>
        </div>
        <div class="float-left mr-3">
            <label>目前器官組織</label>
            <x-adminlte-select2 name="organ_id" igroup-size="sm">
                <option value="-1">全部</option>
                @if(!empty($filters['bodies'][request()->input('body_id')]->organs))
                    @foreach($filters['bodies'][request()->input('body_id')]->organs as $organ)
                        <option
                            value="{{$organ['id']}}" {{request()->input('organ_id') == $organ['id'] ? 'selected' : ''}}>{{$organ['name']}}</option>
                    @endforeach
                @endif
            </x-adminlte-select2>
        </div>
        <div class="float-left mr-3">
            <x-button type="submit" name="查詢" />
        </div>
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
                                        class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增疾病
                                </button>
                            </div>
                            <div class="float-left mr-3">
                                <a class="btn btn-block btn-info btn-flat float-right px-4 " href="{{route('organs.index', ['body_id' => request()->input('body_id')])}}" role="button">點我回器官與組織</a>
                            </div>
                            <div class="float-left mr-3">
                                <button type="button"
                                        class="btn btn-block btn-danger btn-flat float-right px-4 saveAllBtn">儲存排序
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
                <form id="encyclopedia_sickness">
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
                                    {!! Form::input('hidden', "encyclopedia_sickness[".($key+1)."][id]", $data->id ) !!}
                                </td>
                                <td class="align-middle" style="width:25%;">
                                    {{ $data->name }}
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
    </script>
    <script>




        var bodyOrganMappings = JSON.parse('@json($filters)')
        $(document).ready(function(){
            $('#body_id').change(function(){
                $('#organ_id').html('<option value="-1">全部</option>')

                bodyOrganMappings.bodies[$('#body_id').val()]['organs'].forEach((element) => {
                    option = document.createElement('option')
                    option.text = element.name
                    option.value = element.id
                    document.getElementById('organ_id').appendChild(option)
                })
            })
         
        })

        // 解決不能使用兩個modal popup問題
        $(document).on('show.bs.modal', '.modal', function (event) {
            $(this).appendTo($('body'));
        }).on('shown.bs.modal', '.modal.in', function (event) {
            setModalsAndBackdropsOrder();
        }).on('hidden.bs.modal', '.modal', function (event) {
            setModalsAndBackdropsOrder();
        });

        function setModalsAndBackdropsOrder() {
            var modalZIndex = 1040;
            $('.modal.in').each(function (index) {
                var $modal = $(this);
                modalZIndex++;
                $modal.css('zIndex', modalZIndex);
                $modal.next('.modal-backdrop.in').addClass('hidden').css('zIndex', modalZIndex - 1);
            });
            $('.modal.in:visible:last').focus().next('.modal-backdrop.in').removeClass('hidden');
        }

        // 解決不能使用兩個modal popup問題
        $(document).ready(function () {
            //圖庫相關操作
            $('.modal-dialog').width('70%');
            $('.modal-dialog').css('max-width', '80%');

            $('select[name=select_type]').on('change', function () {
                var search_parameter = '';
                if (this.value != 1) {
                    search_parameter += '?select_type=' + this.value;
                }
                window.location.href = '{{URL::current()}}' + search_parameter;
            });

            $('.reset').on('click', function (e) {
                location.href = "{{route('app_activities.index')}}" + '?select_type=0';
            });

            $('select[name=type_url]').on('change', function () {
                if (this.value == 1) {
                    showArticleType();
                } else {
                    showUrlType();
                }
            });

            //  Model hide 事件
            $('#editModal').on('hidden.bs.modal', function () {
                reset_modal();
            })

            $('.createBtn').on('click', function () {
                reset_modal();
                
                $("#selectAddContent").html('');
                $(".create_body_id").val($("#body_id").val());
                $('.create_body_id').change();
                if ($("#organ_id").val() != -1) {
                    $('.create_organ_id').val($("#organ_id").val());
                    $(".selectAddBtn").click();
                }
                changeBlockClass();
                $('#editModal').modal('show');
            })

            $('.editBtn').on('click', function () {
                var edit_id = $(this).data('id');
                var editUrl = "{{url()->current()}}/" + edit_id + '/edit';
                $.get(editUrl, function (data) {
                    reset_modal();
                    $('#editModalTitle').html('編輯醫學百科-疾病');
                    $.each(data, function (index, child) {
                        $('#' + index).val(child);
                    })
                    $('#editId').val(edit_id);
                    $("#status").val(data.status).change();

                    $("#selectAddContent").html('');
                    $(".create_body_id").val($("#body_id").val());
                    $('.create_body_id').change();

                    // console.log(data.organs);
                    $.each(data.organs, function (index, value){
                        let tag = '<li data-id="'+value.id+'">'+value.name+' <a class="removeTag" href="javascript:;"><span class="fa fa-solid fa-trash" ></span></a></li>';
                        $("#selectAddContent").append(tag);
                    })
                    changeBlockClass();
                    $('.removeTag').on('click', function(){
                        $(this).parent().remove();
                        changeBlockClass();
                    })
                    

                    if (data.released <= '{{date("Y-m-d H:i:s")}}') {
                        $('input[name=released]').attr('readonly', true);
                    }

                    if (data.type_url == 1) {
                        showArticleType();
                    } else {
                        showUrlType();
                    }
                    $('#editModal').modal('show');
                })
            })

            $('.saveAllBtn').on('click', function () {
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

        function showArticleType() {
            $('input[name=url]').parent().css('display', 'none');
            $('input[name=articles_id]').parent().css('display', 'block');
        }

        function showUrlType() {
            $('input[name=url]').parent().css('display', 'block');
            $('input[name=articles_id]').parent().css('display', 'none');
        }

        function reset_modal() {
            $('#validateMsg').html('');
            $('#validateMsg').css('display', 'none');
            $('#editModalTitle').html('新增醫學百科-疾病');
            $('#editId').val('');
            $('#editForm input[type="text"]').val('');
            $('#editForm textarea').val('');
            $("#editForm select option:first").prop("selected", 'selected');
            $('#editForm input[name=released]').val('{{date("Y-m-d H:i:s")}}');
            $('#editForm input[name=end]').val('{{date("Y-m-d H:i:s", strtotime("+7 days"))}}');
            $('#editForm input[name=released]').attr('readonly', false);
            $('#imageModal').modal('hide');
            $("#review_image_div").hide();
            showUrlType();
        }

        function saveAllAction() {



            let formData = new FormData($('#encyclopedia_sickness')[0]);

            let sortId = new Array();

            $("form#encyclopedia_sickness tbody tr").each(function (idx) {
                sortId.push($(this).attr('data-id'));
            });

            formData.append('sortId', sortId);

            let ajaxUrl2 = '';
            ajaxUrl2 = "{{url()->current()}}/" + 'sort';
            formData.append('_method', 'PATCH');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('#encyclopedia_sickness input[name="_token"]').val()
                }
            });

            $.ajax({
                data: formData,
                url: ajaxUrl2,
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

        function saveAction() {


            if ($("#selectAddContent li").length < 1) {
                Swal.fire({
                    title: '至少選擇一個對應的器官組織',
                    icon: 'error',
                    confirmButtonText: `確定`,
                    confirmButtonColor: '#f87e6c',
                })
                $('#saveBtn').prop('disabled', false);
                return;
            }


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


            let create_organ_id_array = [];
            $("#selectAddContent li").each(function(index, value){
                create_organ_id_array.push($(this).data('id'))

            })
            create_organ_id_string = create_organ_id_array.join();
            
            formData.append('create_organ_id_string', create_organ_id_string);

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

            let validateMsg = '<ul class="mb-0">';

            $.each(errorMessage, function (key, value) {
                $.each(value, function (k, v) {
                    validateMsg += '<li>' + value + '</li>';
                });
            });

            validateMsg += '</ul>'

            $('#validateMsg').html(validateMsg);
            $('#validateMsg').css('display', 'block');
        }





        $(document).ready(function(){

            // 新增資料的下拉選單
            $('.create_body_id').change(function(){
                $('.create_organ_id').html('<option value="-1">請選擇</option>')
                bodyOrganMappings.bodies[$('.create_body_id').val()]['organs'].forEach((element) => {
                    option = document.createElement('option')
                    option.text = element.name
                    option.value = element.id
                    $(".create_organ_id").append(option)
                })
            })

            // 新增綁定的器官組織
            $(".selectAddBtn").click(function(){

                // 檢查有沒有選擇
                if ($(".create_organ_id").val() == '-1') {
                    alert('請先選擇器官組織');
                    return;
                }


                // 檢查下方是否有已加入的器官組織
                if ($("#selectAddContent li").length > 0) {
                    let exists = false;
                    $("#selectAddContent li").each(function(){
                        if ($(".create_organ_id").val() == $(this).data('id')) {
                            exists = true;
                        }
                    })
                    if (exists) {
                        alert($(".create_organ_id option:selected").text()+' 已有加入，請選擇其他器官組織');
                        return;
                    }
                }

                // 要加入的html
                let tag = '<li data-id="'+$(".create_organ_id").val()+'">'+$(".create_organ_id option:selected").text()+' <a class="removeTag" href="javascript:;"><span class="fa fa-solid fa-trash" ></span></a></li>';
                $("#selectAddContent").append(tag);

                // 加入的html觸發的事件（移除）
                $(".removeTag").unbind('click');
                $('.removeTag').on('click', function(){
                    $(this).parent().remove();
                    changeBlockClass();
                })
                changeBlockClass();
            });

        })
            // 檢查是否有器官組織，沒有的話移除樣式
            function changeBlockClass()
            {
                if ($("#selectAddContent li").length > 0) {
                    $("#selectAddBlock").addClass('block');
                } else {
                    $("#selectAddBlock").removeClass('block');
                }
            }
    </script>
@stop
