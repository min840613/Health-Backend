@extends('adminlte::page')

@section('title', '小工具量測管理')

@section('content_header')
    <h1>小工具量測管理</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @can($role_name.'-create')
                <div class="float-left">
                    <a class="btn btn-block btn-success btn-flat float-right px-4" href="{{ route('measure.create') }}">新增</a>
                </div>
                @endcan
                <div class="float-left ml-3">
                    @can($role_name.'-edit')
                    <button type="button" class="btn btn-block btn-primary btn-flat float-right px-4 saveAllBtn">儲存排序</button>
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
                <form id="measure">
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
                                        {!! Form::input('hidden', "data[".($key+1)."][id]", $data->id ) !!}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data['title'] }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="zoomImage" style="background-image:url(<?= $data->image?>)" /></div>
                                    </td>
                                    <td class="align-middle">
                                        {{ $data['start'] }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data['end'] }}
                                    </td>
                                    <td class="align-middle">
                                        {!! $data->StatusCss !!}
                                    </td>
                                    <td class="align-middle">
                                        <a class="btn btn-secondary btn-flat" href="{{ route('measure.show', $data->id) }}">預覽</a>
                                        @can('measure-edit')
                                        <a class="btn btn-primary btn-flat" href="{{ route('measure.edit', $data->id) }}">編輯</a>
                                        @endcan
                                    </td>
                                    <td class="align-middle">
                                        {!! Form::input('hidden', "data[".($key+1)."][id]", $data->id ) !!}
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
    });

    function saveAllAction()
    {
        let measureFormData = new FormData($('#measure')[0]);

        let sortId = new Array();

        $("form#measure tbody tr").each(function(idx){
            sortId.push($(this).attr('data-id'));
        });

        measureFormData.append('sortId', sortId);

        let ajaxUrl = '';
        ajaxUrl = "{{url()->current()}}/"+'all';
        measureFormData.append('_method', 'PATCH');


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#measure input[name="_token"]').val()
            }
        });

        $.ajax({
            data: measureFormData,
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
