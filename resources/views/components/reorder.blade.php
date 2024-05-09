@extends('adminlte::page')

@section('title', $__env->yieldContent('title'))

@section('content_header')
    @yield('content_header')
@stop

@section('content')
    <div class="card w-100" style="width: 18rem;">
        <div class="card-body">
            <h5 class="card-title mb-3">請以拖曳的方式重新排序。</h5>
            <div class="card-text">
                <div class="list-group-item d-flex">
                    @yield('reorder_header')
                </div>
                {!! Form::open(['url' => $action, 'method' => $method]) !!}
                <x-laravel-blade-sortable::sortable as="div" class="list-group" name="sort_order">
                    @yield('reorder_content')
                </x-laravel-blade-sortable::sortable>
            </div>
            @if(isset($parent_id) && $parent_id !== '')
            <input type="hidden" name="parent_id" value="{{ $parent_id }}">
            @endif
            @if(isset($_GET['page']))
            {!! Form::input('hidden', 'page', isset($_GET['page'])?$_GET['page']:NULL) !!}
            @endif
            <div class="card-footer">
                {!! Form::input('hidden', 'table', $table) !!}
                <button type="submit" class="btn btn-primary btn-flat mr-4">儲存</button>
                <a href="{{ route($site_name.'.index',['page' => isset($_GET['page'])?$_GET['page']:NULL]) }}">
                    <button type="button" class="btn btn-outline-info btn-flat">返回</button>
                </a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@stop
@section('css')

@stop
@section('js')
    <script src="{{ asset('./js/alpine.min.js') }}"></script>
    <x-laravel-blade-sortable::scripts />
    <script src="{{ asset('./js/sortable.js') }}"></script>
@stop
