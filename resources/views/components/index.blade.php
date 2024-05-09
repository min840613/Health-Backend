@extends('adminlte::page')

@section('title', $__env->yieldContent('title'))

@section('content_header')
    @yield('content_header')
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @if(!isset($create) || $create == true)
                @can($role_name.'-create')
                <div class="float-left mr-3">
                    @if(!$__env->yieldContent('create'))
                    <a href="{{ route($site_name.'.create',['page' => isset($_GET['page'])?$_GET['page']:NULL,'keywords' => isset($_GET['keywords'])?$_GET['keywords']:NULL,'parent_id' => isset($_GET['parent_id'])?$_GET['parent_id']:NULL]) }}"><button type="button"
                            class="btn btn-block btn-success btn-flat float-right px-4">新增</button></a>
                    @else
                        @yield('create')
                    @endif
                </div>
                @endcan
                @endif
                @can($role_name.'-edit')
                @if(isset($reorder_path) && $reorder_path)
                    <div class="float-left mr-3">
                    @if(!$__env->yieldContent('reorder'))
                        <a href="{{ url($reorder_path.(isset($_GET['page'])?'?page='.$_GET['page']:NULL)) }}">
                            <button type="button" class="btn btn-block btn-secondary btn-flat float-right px-4">排序</button>
                        </a>
                    @else
                    @yield('reorder')
                    @endif
                    </div>
                @endif
                @if(isset($push) && $push == true)
                <div class="float-left mr-3">
                    @if(!$__env->yieldContent('push'))
                    <a href="{{ url($push) }}">
                        <button type="button" class="btn btn-block btn-info btn-flat px-4">新增推播</button>
                    </a>
                    @else
                        @yield('push')
                    @endif
                </div>
                @endif
                @endcan
                <div class="float-left mr-3">
                    @yield('otherBtn')
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
                @yield('headerOthers')
            </div>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p class="m-0">{{ $message }}</p>
                </div>
            @endif
            <div class="card-body p-0">
                <table class="table">
                    <thead>
                        <tr>
                            @if(isset($has_checkbox) && $has_checkbox === true)
                                <th style="width: 10px"><button class="btn btn-sm btn-outline-primary select-all">全選</button></th>
                            @endif
                            @if(!isset($no_id) || $no_id == false)
                            <th style="width: 10px">ID</th>
                            @endif
                            @foreach ($field as $value)
                                <th >{{ $value }}</th>
                            @endforeach
                            @if(!isset($has_act) || $has_act == true)
                                <th>動作</th>
                            @endif
                            @if(isset($has_sort) && $has_sort === true)
                                <th >排序</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @yield('data_list')
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {!! $datas->withQueryString()->render() !!}

    @yield('modal')
@stop

@section('css')
    @yield('js')
@stop

@section('js')
    @yield('js')
@stop
