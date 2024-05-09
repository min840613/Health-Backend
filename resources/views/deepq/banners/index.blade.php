@extends('adminlte::page')

@section('title', 'AI-Banner管理')

@section('content_header')
    <h1>AI-Banner管理</h1>

    @if(isset($_GET['select_type']) && $_GET['select_type'] == 0)
        {{ Form::open(['route' => 'deepq.banners.index', 'method' => 'get', 'style' => 'border:1px solid #ccc; padding: 5px;']) }}

        {!! Form::hidden('select_type', $_GET['select_type']) !!}
        @foreach($searchField as $key => $value)
            @if($value['name'] == 'search_title')
                <div class="row col-6" style="padding-top: 10px; margin-bottom: 10px;">
                    <div class="col-6">
                        <label>{{$value['title']}}</label>
                        {!! Form::input('text', $value['name'], isset($_GET['search_title']) ? $_GET['search_title'] : '', ['placeholder' => isset($value['placeholder'])?$value['placeholder']:$value['name'], 'class' => isset($value['class'])?'form-control '.$value['class']:'form-control' , 'id' => isset($value['id'])?$value['id']:'']) !!}
                    </div>
                </div>
            @else
                <div class="row col-9"  style="margin-bottom: 20px;">
                    @foreach($value['elements'] as $k => $v)
                        @if($k == 'start')
                            <div class="col-4">
                                <label>{{$value['title']}}</label>

                                @if (isset($_GET['search_start']) && !empty($_GET['search_start']))
                                    <input type="hidden" id="search_start_origin" value="{{$_GET['search_start']}}" >
                                @else
                                    <input type="hidden" id="search_start_origin" value="{{ date('Y-m-d H:i:s', strtotime('-7 days')) }}" >
                                @endif

                                <x-adminlte-date-range name="{{$v['name']}}">
                                    <x-slot name="appendSlot">
                                    <div class="input-group-text bg-dark">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    </x-slot>
                                </x-adminlte-date-range>
                            </div>
                        @else
                            <div class="col-4">
                                <label>&nbsp;</label>

                                @if (isset($_GET['search_end']) && !empty($_GET['search_end']))
                                    <input type="hidden" id="search_end_origin" value="{{$_GET['search_end']}}" >
                                @else
                                    <input type="hidden" id="search_end_origin" value="{{ date('Y-m-d H:i:s') }}" >
                                @endif

                                <x-adminlte-date-range name="{{$v['name']}}">
                                    <x-slot name="appendSlot">
                                    <div class="input-group-text bg-dark">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    </x-slot>
                                </x-adminlte-date-range>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @endforeach
        <div class="row col-6">
            <div class="col-2">
                <button class="mr-1 btn btn-primary">查詢</button>
            </div>
            <div class="col-3">
                <button type = 'button' class="reset mr-1 btn btn-warning">取消查詢</button>
            </div>
        </div>
        {!! Form::close() !!}
    @endif
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @if(!isset($create) || $create == true)
                @can($role_name.'-create')
                @if(!isset($_GET['select_type']) || $_GET['select_type'] == 1)
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
                    </div>
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-block btn-primary btn-flat float-right px-4 saveAllBtn">儲存排序</button>

                    </div>
                @endif
                @endcan
                @endif
                <div class="float-right ml-1 d-flex">
                    <div class="input-group btn btn-warning">
                        {{$select_type['title']}}：
                        {!! Form::select($select_type['name'], $select_type['option'], isset($_GET['select_type']) ? $_GET['select_type'] : 1, ['class' => 'px-4']) !!}
                    </div>
                </div>
                @if(isset($search) && $search == true)
                <div class="float-right ml-1 d-flex">
                    @if(!$__env->yieldContent('push'))
                    @if($keywords)
                    <button class="reset_search mr-1 btn btn-outline-secondary">重置搜尋</button>
                    @endif
                    {!! Form::open(['method' => 'GET', 'route' => [$site_name.'.index'],'id' => 'search_box']) !!}
                    <div class="input-group">
                        {!! Form::text('keywords','', ['class' => 'form-control','placeholder'=>($keywords)?$keywords:"Search"]) !!}
                        <div class="input-group-append">
                            {!! Form::submit('搜尋', ['class' => 'btn btn-secondary']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                    @else
                        @yield('search')
                    @endif
                </div>
                @endif
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                @foreach ($datas as $key => $data)

                @endforeach
                <form id="deepq_banners">
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
                            @if($defaultData)
                                <tr>
                                    <th class="align-middle">預設</th>
                                    <th class="align-middle">{{$defaultData->title}}</th>
                                    <th class="align-middle"><img src="{{$defaultData->image}}" style="width: 100px"></th>
                                    <th class="align-middle">預設</th>
                                    <th class="align-middle">預設</th>
                                    @if(!isset($_GET['select_type']) || $_GET['select_type'] == 1)
                                        <th class="align-middle">
                                            {!! $defaultData->StatusCss !!}
                                        </th>
                                        <th class="align-middle">
                                            <a class="btn btn-secondary btn-flat" href="{{ route('deepq.banners.show', $defaultData->id) }}">預覽</a>
                                            @can($role_name.'-edit')
                                                <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$defaultData->id}}">編輯</button>
                                            @endcan
                                        </th>
                                    @endif
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @if(!$defaultData && (!isset($_GET['select_type']) || $_GET['select_type'] == 1))
                                <tr class="align-middle" style="width:75%;">
                                    <td colspan="8" style="text-align: center; color: rgb(193, 0, 0);" class="align-middle">新增之第一筆將自動成為預設</td>
                                </tr>
                            @endif
                            @foreach ($datas as $key => $data)
                                <tr data-id="{{$key+1}}">
                                    <td class="align-middle">
                                        {{ $key+1 }}
                                        {!! Form::input('hidden', "deepq_banners[".($key+1)."][id]", $data->id ) !!}
                                    </td>
                                    <td class="align-middle" style="width:25%;">
                                        {{ $data->title }}
                                    </td>
                                    <td class="align-middle">
                                        <img src="{{$data->image}}" style="width: 100px">
                                    </td>
                                    <td class="align-middle">
                                        {{ $data->start }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data->end }}
                                    </td>
                                    @if(!isset($_GET['select_type']) || $_GET['select_type'] == 1)
                                        <td class="align-middle">
                                            {!! $data->StatusCss !!}
                                        </td>
                                        <td class="align-middle">
                                            <a class="btn btn-secondary btn-flat" href="{{ route('deepq.banners.show', $data->id) }}">預覽</a>
                                            @can($role_name.'-edit')
                                                <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
                                            @endcan
                                        </td>
                                        <td class="align-middle">
                                            @can($role_name.'-edit')
                                                <div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    @if(isset($_GET['select_type']) && $_GET['select_type'] == 0)
        {!! $datas->render() !!}
    @endif
    @include('components.formbase_modal')
@stop

@include('components.gallery_modal')

@section('css')
    @yield('js')
@stop

@section('js')
<script>
    //排序功能
    $(".sort_table tbody").sortable({handle: ".handle"});
</script>
<script>

    // 解決不能使用兩個modal popup問題
    $(document).on('show.bs.modal', '.modal', function(event) {
        $(this).appendTo($('body'));
    }).on('shown.bs.modal', '.modal.in', function(event) {
        setModalsAndBackdropsOrder();
    }).on('hidden.bs.modal', '.modal', function(event) {
        setModalsAndBackdropsOrder();
    });

    function setModalsAndBackdropsOrder() {
        var modalZIndex = 1040;
        $('.modal.in').each(function(index) {
            var $modal = $(this);
            modalZIndex++;
            $modal.css('zIndex', modalZIndex);
            $modal.next('.modal-backdrop.in').addClass('hidden').css('zIndex', modalZIndex - 1);
        });
        $('.modal.in:visible:last').focus().next('.modal-backdrop.in').removeClass('hidden');
    }
    // 解決不能使用兩個modal popup問題

    $(document).ready(function() {

       $('input[name="search_start"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            startDate:  $("#search_start_origin").val(),
            timePicker: true,
            timePicker24Hour: true,
            cancelButtonClasses: "btn-danger",
            drops:'up',
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        });
       $('input[name="search_end"]').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            startDate:  $("#search_end_origin").val(),
            timePicker: true,
            timePicker24Hour: true,
            cancelButtonClasses: "btn-danger",
            drops: "up",
            locale: {
                format: 'YYYY-MM-DD HH:mm'
            }
        });


        //圖庫相關操作
        $("#review_image_click").click(function(){
            if($("#image").val()){
                $("#review_image").attr("src",$("#image").val());
                $("#review_image_div").show();
            }
        })
        $('#gallery_image').on('click', function() {
            var parentId = $(this).parent('div').prev('input').attr('id');
            console.log(parentId);
            $('#galleryModal iframe').attr('data-parent', parentId );
            $('#galleryModal').modal('show');
        })

        window.addEventListener("message", (e) => {
            if(e.origin !== 'tvbs.com.tw' || e.origin !== 'test.health-backstage.com') {
                $("#" + e.data.parent).val(e.data.imgUrl);
                $(".gallery-modal-header > .close").trigger('click');
                $("#review_image_div").hide();
            }
        });
        //圖庫相關操作


        $('.modal-dialog').width('70%');
        $('.modal-dialog').css('max-width', '80%');

        $('select[name=select_type]').on('change', function() {
            var search_parameter = '';
            if(this.value != 1){
                search_parameter += '?select_type='+this.value;
            }
            window.location.href = '{{URL::current()}}' + search_parameter;
        });

        $('.reset').on('click', function(e){
            location.href = "{{route('deepq.banners.index')}}" + '?select_type=0';
        });

        //  Model hide 事件
        $('#editModal').on('hidden.bs.modal', function () {
            reset_modal();
        })

        $('.createBtn').on('click', function() {
            reset_modal();
            $('input[name="start"]').prop('disabled', false);
            $('input[name="start"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  moment().startOf('hour'),
                timePicker: true,
                timePicker24Hour: true,
                drops: "up",
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
           $('input[name="end"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  moment().add(7, 'days').startOf('hour'),
                timePicker: true,
                timePicker24Hour: true,
                drops: "up",
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
            $('#editModal').modal('show');
        })

        $('.editBtn').on('click', function() {
            var edit_id = $(this).data('id');
            var editUrl = "{{url()->current()}}/"+ edit_id + '/edit';
            $.get(editUrl, function (data) {
                reset_modal();
                $('#editModalTitle').html('AI - 編輯Banner');
                $.each(data,function(index, child){
                    $('#'+index).val(child);
                })
                $('#editId').val(edit_id);

                $('input[name="start"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate:  data.start,
                    timePicker: true,
                    timePicker24Hour: true,
                    drops: "up",
                    cancelButtonClasses: "btn-danger",
                    locale: {
                        format: 'YYYY-MM-DD HH:mm'
                    },
                });
                if(data.start <= '{{date("Y-m-d H:i:s")}}'){
                    $('input[name="start"]').prop('disabled', true);
                }

               $('input[name="end"]').daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    startDate:  data.end,
                    timePicker: true,
                    timePicker24Hour: true,
                    drops: "up",
                    cancelButtonClasses: "btn-danger",
                    locale: {
                        format: 'YYYY-MM-DD HH:mm'
                    }
                });
                $('#editModal').modal('show');
            })
        })

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
        $('#editModalTitle').html('AI - 新增Banner');
        $('#editId').val('');
        $('#editForm input[type="text"]').val('');
        $('#editForm textarea').val('');
        $("#editForm select option:first").prop("selected", 'selected');
        $('#editForm input[name=start]').val('{{date("Y-m-d H:i:s")}}');
        $('#editForm input[name=start]').val('{{date("Y-m-d H:i:s", strtotime("+7 days"))}}');
        $('#editForm input[name=start]').attr('readonly', false);
        $('#imageModal').modal('hide');
        $("#review_image_div").hide();
    }

    function saveAllAction()
    {
        let DeepqBannersFormData = new FormData($('#deepq_banners')[0]);

        let sortId = new Array();

        $("form#deepq_banners tbody tr").each(function(idx){
            sortId.push($(this).attr('data-id'));
        });

        DeepqBannersFormData.append('sortId', sortId);

        let ajaxUrl2 = '';
        ajaxUrl2 = "{{url()->current()}}/"+'save_sort';
        DeepqBannersFormData.append('_method', 'POST');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#deepq_banners input[name="_token"]').val()
            }
        });

        $.ajax({
            data: DeepqBannersFormData,
            url: ajaxUrl2,
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

    function saveAction()
    {
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');

        var startDisabled =  $('input[name="start"]').prop('disabled');
        console.log('Disabled',startDisabled);

        $('input[name="start"]').prop('disabled', false);

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
                $('#saveBtn').prop('disabled', false);
                $('input[name="start"]').prop('disabled', startDisabled);
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
