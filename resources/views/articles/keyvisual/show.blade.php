@extends('adminlte::page')

@section('title', '頭條管理')

@section('content_header')
    <h1>頭條管理 - 預覽</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            @foreach($field as $v)
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" for="inputName">
                    {{$v['title']}}
                    @if($v['is_required'])
                    <span class="text-danger">*</span>
                    @endif
                </label>
                <div class="col-sm-10">
                    @switch($v['title'])
                        @case('文章標題')
                            {{ $data->title }}
                            @break
                        @case('頭條URL')
                            {{ $data->link }}
                            @break
                        @case('首頁主圖路徑')
                            {{ $data->image }}<br>
                            <img style="width: auto;" src="{{ $data->image }}"/>
                            @break
                        @case('發佈時間')
                            {{ $data->start }}<br>
                            @break
                        @case('下架時間')
                            {{ $data->end }}<br>
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