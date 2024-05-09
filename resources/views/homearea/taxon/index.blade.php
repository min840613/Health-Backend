@extends('adminlte::page')

@section('title', '首頁分類單元管理')

@section('content_header')
    <h1>首頁分類單元管理</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @if(!isset($create) || $create == true)
                @can($role_name.'-create')
                    <div class="float-left mr-3">
                        <a class="btn btn-block btn-success btn-flat float-right px-4" href="{{ route('home_taxon.create') }}">新增</a>
                        {{-- <button type="button" class="btn btn-block btn-success btn-flat float-right px-4 createBtn">新增</button> --}}
                    </div>
                @endcan
                @can($role_name.'-edit')
                    <div class="float-left mr-3">
                        <button type="button" class="btn btn-block btn-danger btn-flat float-right px-4 saveSortBtn">儲存排序</button>
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
                <form id="home_taxon">
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
                                        {!! Form::input('hidden', "homeTaxon[".($key+1)."][id]", $data->id ) !!}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data->name }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $mainCategories[$data->categories_id] }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $data->article_id }}
                                    </td>
                                    <td class="align-middle">
                                        @if($data->article)
                                            <img src="{{$data->article->image}}" width="100px"/>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($data->published_at)
                                            {{$data->published_at}} <br>～ {{$data->published_end}}
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                       {!! $data->CategoriesStatusCss !!}
                                    </td>
                                    <td class="align-middle">
                                        @can($role_name.'-edit')
                                            <a class="btn btn-primary btn-flat mr-4" href="{{ route('home_taxon.edit', $data->id) }}">編輯</a>
                                        @endcan
                                    </td>
                                    <td class="align-middle">
                                        @can($role_name.'-edit')
                                            <div class="handle health_sort"><i class="fa fa-fw fa-sort"></i></div>
                                        @endcan
                                    </td>
                                    {{-- <td class="align-middle">
                                        {!! Form::input('text', "menu[".($key+1)."][title]", htmlspecialchars($data->title, ENT_QUOTES, 'UTF-8') ) !!}
                                    </td>
                                    <td class="align-middle">
                                        {!! Form::select("menu[".($key+1)."][layout]", $layoutOption, $data->layout) !!}
                                    </td>
                                    <td class="align-middle">
                                        {!! Form::select("menu[".($key+1)."][menu_list_status]", ['1' => '顯示', '0' => '不顯示'], $data->menu_list_status) !!}
                                    </td> --}}

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

    $(document).ready(function() {

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
        let taxonFormData = new FormData($('#home_taxon')[0]);

        let sortId = new Array();

        $("form#home_taxon tbody tr").each(function(idx){
            sortId.push($(this).attr('data-id'));
        });

        taxonFormData.append('sortId', sortId);

        let ajaxUrl = '';
        ajaxUrl = "{{route('home_taxon.save_sort')}}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#home_taxon input[name="_token"]').val()
            }
        });

        $.ajax({
            data: taxonFormData,
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
