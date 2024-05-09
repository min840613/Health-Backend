@extends('adminlte::page')

@section('title', '醫級專家-Banner管理')

@section('content_header')
    <h1>醫級專家-Banner管理</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @if(!isset($create) || $create == true)
                @can($role_name.'-create')
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button>
                    </div>
                @endcan
                @can($role_name.'-edit')
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-block btn-primary btn-flat float-right px-4 saveAllBtn">儲存排序</button>

                    </div>
                @endcan
                @endif
                <div class="float-right ml-1 d-flex">
                    <div class="input-group btn btn-warning">
                        {{$select_status['title']}}：
                        {!! Form::select($select_status['name'], $select_status['option'], isset($_GET['select_status']) ? $_GET['select_status'] : 1, ['class' => 'px-4']) !!}
                    </div>
                </div>
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                <form id="masters_banner">
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
                        </thead>
                        <tbody>
                            @foreach ($datas as $key => $data)
                                <tr data-id="{{$key+1}}">
                                    <td class="align-middle">
                                        {{ $key+1 }}
                                        {!! Form::input('hidden', "masters_banner[".($key+1)."][id]", $data->id ) !!}
                                    </td>
                                    <td class="align-middle">
                                        <img src="{{$data->image}}" style="width: 100px">
                                    </td>
                                    <td class="align-middle">
                                        <img src="{{$data->mobile_image}}" style="width: 100px">
                                    </td>
                                    <td class="align-middle">
                                        {{ $data->published_at->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data->published_end->format('Y-m-d H:i') }}
                                    </td>
                                    <td class="align-middle">
                                        {!! $data->StatusCss !!}
                                    </td>
                                    <td class="align-middle">
                                        <a class="btn btn-secondary btn-flat" href="{{ route('masters_banner.show', $data->id) }}">預覽</a>
                                        @can($role_name.'-edit')
                                            <button type="button" class="btn btn-primary btn-flat mr-4 editBtn" data-id="{{$data->id}}">編輯</button>
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

    @include('masters.banner.modal')
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
        //圖庫相關操作
        $("#review_image_click").click(function(){
            if($("#image").val()){
                $("#review_image").attr("src",$("#image").val());
                $("#review_image_div").show();
            }
        })
        $("#review_mobile_image_click").click(function(){
            if($("#mobile_image").val()){
                $("#review_mobile_image").attr("src",$("#mobile_image").val());
                $("#review_mobile_image_div").show();
            }
        })
        $('#gallery_image').on('click', function() {
            var parentId = $(this).parent('div').prev('input').attr('id');
            console.log(parentId);
            $('#galleryModal iframe').attr('data-parent', parentId );
            $('#galleryModal').modal('show');
        })

        $('#gallery_mobile_image').on('click', function() {
            var parentId = $(this).parent('div').prev('input').attr('id');
            console.log(parentId);
            $('#galleryModal iframe').attr('data-parent', parentId );
            $('#galleryModal').modal('show');
        })

        window.addEventListener("message", (e) => {
            if(e.origin !== 'tvbs.com.tw' || e.origin !== 'test.health-backstage.com') {
                $("#" + e.data.parent).val(e.data.imgUrl);
                $(".gallery-modal-header > .close").trigger('click');
                $("#review_"+e.data.parent+"_div").hide();
            }
        });
        //圖庫相關操作


        $('.modal-dialog').width('70%');
        $('.modal-dialog').css('max-width', '80%');

        $('select[name=select_status]').on('change', function() {
            var search_parameter = '';
            if(this.value != 1){
                search_parameter += '?select_status='+this.value;
            }
            window.location.href = '{{URL::current()}}' + search_parameter;
        });

        $('.reset').on('click', function(e){
            location.href = "{{route('masters_banner.index')}}" + '?select_status=1';
        });

        $('select[name=type]').on('change', function() {
            if(this.value == 0){
                showInterior();
            }else{
                showOutside();
            }
        });

        //  Model hide 事件
        $('#editModal').on('hidden.bs.modal', function () {
            reset_modal();
        })

        $('.createBtn').on('click', function() {
            reset_modal();
            getMaster(0,0);
            $('#editModal').modal({
                backdrop:"static",
                keyboard:false,
                show: true
            });
        })

        $('#division_id').on('change', function(e, switch_for_getMaster) {

            if(switch_for_getMaster == undefined || switch_for_getMaster){
                $('#division_id').attr('readonly', true);
                $('#institution_id').attr('readonly', true);
                getMaster($(this).val(), $('#institution_id').val());
            }
        })

        $('#institution_id').on('change', function(e, switch_for_getMaster) {

            if(switch_for_getMaster == undefined || switch_for_getMaster){
                $('#division_id').attr('readonly', true);
                $('#institution_id').attr('readonly', true);
                getMaster($('#division_id').val(), $(this).val());
            }
        })

        $('.editBtn').on('click', function() {
            var edit_id = $(this).data('id');
            var editUrl = "{{url()->current()}}/"+ edit_id + '/edit';
            $.get(editUrl, function (data) {
                reset_modal();
                $('#editModalTitle').html('編輯Banner');
                $.each(data,function(index, child){
                    if(index == 'division_id' || index == 'institution_id' || index == 'master_id'){
                        if(child == null){
                            $('#'+index).val(0).trigger('change', false);
                        }else{
                            $('#'+index).val(child).trigger('change', false);
                        }
                    }else{
                        $('#'+index).val(child);
                    }
                })

                getMaster($('#division_id').val(), $('#institution_id').val(), data.master_id);

                $('#editId').val(edit_id);

                if(data.published_at <= '{{date("Y-m-d H:i:s")}}'){
                    $('input[name=published_at]').attr('readonly', true);
                }

                if(data.type == 0){
                    showInterior();
                }else{
                    showOutside();
                }
                $('#editModal').modal({
                    backdrop:"static",
                    keyboard:false,
                    show: true
                });
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

    function showInterior(){
        $('input[name=url]').parent().css('display', 'none');
        $('select[name=division_id]').parent().parent().parent().css('display', 'block');
        $('select[name=institution_id]').parent().parent().parent().css('display', 'block');
        $('select[name=master_id]').parent().parent().parent().css('display', 'block');
    }

    function showOutside(){
        $('input[name=url]').parent().css('display', 'block');
        $('select[name=division_id]').parent().parent().parent().css('display', 'none');
        $('select[name=institution_id]').parent().parent().parent().css('display', 'none');
        $('select[name=master_id]').parent().parent().parent().css('display', 'none');
    }

    function reset_modal(){
        $('#validateMsg').html('');
        $('#validateMsg').css('display', 'none');
        $('#editModalTitle').html('新增Banner');
        $('#editId').val('');
        $('#editForm input[type="text"]').val('');
        $('#editForm textarea').val('');
        $("#editForm select option:first").prop("selected", 'selected');
        $('#institution_id').val(0).trigger('change', false);
        $('#division_id').val(0).trigger('change', false);
        $('#master_id').val(0).trigger('change');
        $('#editForm input[name=published_at]').val('{{date("Y-m-d H:i")}}');
        $('#editForm input[name=published_end]').val('{{date("Y-m-d H:i", strtotime("+7 days"))}}');
        $('#editForm input[name=published_at]').attr('readonly', false);
        $('#imageModal').modal('hide');
        $("#review_image_div").hide();
        showInterior();
    }

    function saveAllAction()
    {
        let MastersBannerFormData = new FormData($('#masters_banner')[0]);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#masters_banner input[name="_token"]').val()
            }
        });

        $.ajax({
            data: MastersBannerFormData,
            url: "{{url()->current()}}/"+'sort',
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

    function getMaster(division_id, institution_id, master_id){
        var editUrl = "{{url()->current()}}/get_master/"+ division_id + '/' + institution_id;

        $.get(editUrl, function (data) {
            console.log(data);
            var option = '<option value="0">請選擇醫師</option>';
            $.each(data, function(index, child){
                option += '<option value="'+child.id+'">'+child.name+'</option>';
            })
            $('#master_id').empty();
            $('#master_id').append(option);

            $('#division_id').attr('readonly', false);
            $('#institution_id').attr('readonly', false);

            if(master_id != undefined && master_id != null){
                $('#master_id').val(master_id).trigger('change');
            }

        })
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
