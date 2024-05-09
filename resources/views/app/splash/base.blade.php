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
                            @foreach ($field as $value)
                            <th>{{ $value }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @yield('data_list')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @yield('modal')
@stop

@section('css')
    @yield('css')
@stop

@section('js')
    @yield('js')
@stop
