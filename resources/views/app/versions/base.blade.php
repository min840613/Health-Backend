@extends('adminlte::page')

@section('title', $__env->yieldContent('title'))

@section('content_header')
    @yield('content_header')
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            <!-- <div class="card-header">

            </div> -->
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
    {!! $datas->render() !!}

    @yield('modal')
@stop

@section('css')
    @yield('css')
@stop

@section('js')
    @yield('js')
@stop
