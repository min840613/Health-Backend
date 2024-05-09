@extends('adminlte::page')

@section('title', 'AI - Banner管理 - 預覽')

@section('content_header')
    <h1>AI - Banner管理 - 預覽</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            @foreach($field as $v)
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="inputName">{{$v}}</label>
                <div class="col-sm-10">
                    @switch($v)
                        @case('活動名稱')
                            {{ $data->title }}
                            @break
                        @case('活動圖')
                            {{ $data->image }}<br>
                            <img style='width: 90%;' src="{{ $data->image }}"/>
                            @break
                        @case('活動上架時間')
                            {{ $data->start }}<br>
                            @break
                        @case('活動下架時間')
                            {{ $data->end }}<br>
                            @break
                        @case('狀態')
                            {!! $data->StatusCss !!}<br>
                            @break
                    @endswitch
                </div>
            </div>
            @endforeach
            <hr>
            <input type="button" name="back" class="btn btn-outline-info" value="返回" onClick="historylist();">
        </div>
   </div>
</div>
@stop

@section('css')
   <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        function historylist(){
            if (history.length > 1) {
                history.back();
            } else {
                window.close();
            }
        }
    </script>
@stop
