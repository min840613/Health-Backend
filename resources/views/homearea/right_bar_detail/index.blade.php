@extends('adminlte::page')

@section('title', '內容置頂')

@section('content_header')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h1>內容置頂{{empty($datas) ? '' : ('：'.$datas->name)}}</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @if(!isset($create) || $create == true)
                @can($role_name.'-create')
                    @if(!empty($datas))
                        <div class="float-left mr-3">
                            <a class="btn btn-block btn-success btn-flat float-right px-4" href="{{ route('detail.create', ['right_bar' => empty($datas) ? 0 : $datas->id]) }}">新增</a>
                        </div>
                    @endif
                @endcan
                @can($role_name.'-edit')
                    @if(!empty($datas))
                        <div class="float-left mr-3">
                            <button type="button" class="btn btn-block btn-danger btn-flat float-right px-4 saveSortBtn">儲存排序</button>
                        </div>
                    @endif
                @endcan
                @endif
                <div class="float-right ml-1 d-flex">
                    <div class="input-group btn btn-warning">
                        版位切換：
                        {!! Form::select('rightBar', $rightBar, empty($datas) ? 0 : $datas->id, ['class' => 'px-4']) !!}
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
                <form id="right_bar_detail">
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
                            @if(!empty($datas))
                            @foreach ($datas->detail as $key => $detail)
                                <tr data-id="{{$key+1}}">
                                    <td class="align-middle">
                                        {{ $key+1 }}
                                        {!! Form::input('hidden', "rightBarDetail[".($key+1)."][id]", $detail->id ) !!}
                                    </td>
                                    <td class="align-middle">
                                        {{ $datas->name }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $detail->article_id }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $detail->name }}
                                    </td>
                                    <td class="align-middle">
                                        <img src="{{ $detail->article->image }}" style="width: 100px">
                                    </td>
                                    <td class="align-middle">
                                        {{ $detail->published_at }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $detail->published_end }}
                                    </td>
                                    <td class="align-middle">
                                        {!! $detail->StatusCss !!}
                                    </td>
                                    <td class="align-middle">
                                        @can($role_name.'-edit')
                                            <a class="btn btn-primary btn-flat mr-4" href="{{ route('detail.edit', ['right_bar' => $datas->id, 'detail' => $detail->id]) }}">編輯</a>
                                        @endcan
                                    </td>
                                    <td class="align-middle">
                                        @can($role_name.'-edit')
                                            <div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            @endif
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

    $(document).ready(function() {

        $('select[name=rightBar]').on('change', function() {
            let location = "{{ route('detail.index', ['right_bar' => ':id']) }}";
            location = location.replace(':id', $(this).val());
            window.location.href = location;
        });

        $('.saveSortBtn').on('click', function() {
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
    });

    function saveSortAction()
    {
        let rightBarDetailFormData = new FormData($('#right_bar_detail')[0]);

        let sortId = new Array();

        $("form#right_bar_detail tbody tr").each(function(idx){
            sortId.push($(this).attr('data-id'));
        });

        rightBarDetailFormData.append('sortId', sortId);

        let ajaxUrl = "{{url()->current()}}/save_sort";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#right_bar input[name="_token"]').val()
            }
        });

        $.ajax({
            data: rightBarDetailFormData,
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
                Swal.fire({
                    title: '儲存失敗',
                    icon: 'error',
                    confirmButtonText: `確定`,
                    confirmButtonColor: '#f87e6c',
                })
            }
        });
    }
</script>
@stop
