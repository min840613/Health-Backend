@extends('adminlte::page')

@section('title', '文章分類管理')

@section('content_header')
    <h1>文章分類管理</h1>
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
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-block btn-danger btn-flat float-right px-4 saveAllBtn">儲存排序</button>
                    </div>
                @endcan
                @endif
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
                <form id="article_type">
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
                                        {!! Form::input('hidden', "articleType[".($key+1)."][id]", $data->id ) !!}
                                    </td>
                                    <td class="align-middle" style="width:25%;">
                                        {{ $data->name }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data->created_at }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data->updated_at }}
                                    </td>
                                    <td class="align-middle">
                                        {!! $data->StatusCss !!}
                                    </td>
                                    <td class="align-middle">
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

    @include('components.formbase_modal')
@stop

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
                $('#editModalTitle').html('編輯文章分類');
                $.each(data,function(index, child){
                    $('#'+index).val(child);
                })
                $('#editId').val(edit_id);
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
        $('#editModalTitle').html('新增文章分類');
        $('#editId').val('');
        $('#editForm input[type="text"]').val('');
        $('#editForm textarea').val('');
        $("#editForm select option:first").prop("selected", 'selected');
    }

    function saveAllAction()
    {
        let ArticleTypeFormData = new FormData($('#article_type')[0]);

        let sortId = new Array();

        $("form#article_type tbody tr").each(function(idx){
            sortId.push($(this).attr('data-id'));
        });

        ArticleTypeFormData.append('sortId', sortId);

        let ajaxUrl2 = '';
        ajaxUrl2 = "{{route('article_type.save_sort')}}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#article_type input[name="_token"]').val()
            }
        });

        $.ajax({
            data: ArticleTypeFormData,
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
