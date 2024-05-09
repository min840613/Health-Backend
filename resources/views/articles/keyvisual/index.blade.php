@extends('adminlte::page')

@section('title', '頭條管理')

@section('content_header')
    <h1>頭條管理</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @can($role_name.'-create')
                <div class="float-left mr-4 d-flex">
                    {!! Form::open(array('url'=>'admin/articles_manage/keyvisual/create','method'=>'get','id'=>'inside_create')) !!}
                        <div class="input-group">
                            <input id="article_id" class="form-control" placeholder="文章ID" name="article_id" type="text" value="">
                            <div class="input-group-append">
                                <input class="btn btn-secondary send_article_id" type="submit" value="ID匯入">
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
                <div class="float-left">
                    <a class="btn btn-block btn-success btn-flat float-right px-4" href="{{ route('keyvisual.create') }}">新增外部連結</a>
                </div>
                @endcan
                <div class="float-right mr-3">
                    @can($role_name.'-edit')
                    <button type="button" class="btn btn-block btn-danger btn-flat float-right px-4 saveAllBtn">排序儲存</button>
                    @endcan
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
            @if ($message = Session::get('error'))
                <div class="alert alert-danger">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                <form id="keyvisual">
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
                                <tr data-id="{{$key+1}}" class="<?= $data['isShow']?'table-primary':''?>">
                                    <td class="align-middle">
                                        {{ $key+1 }}
                                        {!! Form::input('hidden', "data[".($key+1)."][keyvisual_id]", $data->keyvisual_id ) !!}
                                    </td>
                                    <td class="align-middle">
                                        <?= $data->start ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= $data->end ?>
                                    </td>
                                    <td class="align-middle">
                                        <?= $data->title ?>
                                    </td>
                                    <td class="align-middle">
                                        <img style="width: 150px;" src="<?= $data->image?>" />
                                    </td>
                                    <td class="align-middle">
                                        <a class="btn btn-info btn-flat" href="{{ route('keyvisual.show', $data->keyvisual_id) }}">預覽</a>
                                        @can('keyvisual-edit')
                                        <a class="btn btn-primary btn-flat" href="{{ route('keyvisual.edit', $data->keyvisual_id) }}">編輯</a>
                                        @endcan
                                    </td>
                                    <td class="align-middle">
                                        {!! Form::input('hidden', "data[".($key+1)."][keyvisual_id]", $data->keyvisual_id ) !!}
                                        <div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div>
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
    $(".sort_table tbody").sortable({handle: ".handle"});
</script>
<script>
    $(document).ready(function() {

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

        $(".send_article_id").on('click', function(){
            var url = $("#inside_create").attr('action') + "?article_id=" + $("#article_id").val();
            window.location.href = url;
            return false;
        })
    });

    function saveAllAction()
    {
        let keyvisualFormData = new FormData($('#keyvisual')[0]);

        let sortId = new Array();

        $("form#keyvisual tbody tr").each(function(idx){
            sortId.push($(this).attr('data-id'));
        });

        keyvisualFormData.append('sortId', sortId);

        let ajaxUrl = '';
        ajaxUrl = "{{url()->current()}}/"+'all';
        keyvisualFormData.append('_method', 'PATCH');


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#keyvisual input[name="_token"]').val()
            }
        });

        $.ajax({
            data: keyvisualFormData,
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
