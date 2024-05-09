@extends('adminlte::page')

@section('title', '小工具量測管理')

@section('content_header')
    <h1>小工具量測管理 - 預覽</h1>
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
                        @case('量測名稱')
                            <div class="col-form-label">{{ $data->title }}</div>
                            @break
                        @case('量測URL')
                            <div class="col-form-label">{{ $data->link }}</div>
                            @break
                        @case('主圖路徑')
                            <div class="col-form-label">
                                {{ $data->image }}
                                <img style="width: 100%;" src="{{ $data->image }}"/>
                            </div>
                            @break
                        @case('上架時間')
                            <div class="col-form-label">
                                {{ $data->start }}
                            </div>
                            @break
                        @case('下架時間')
                            <div class="col-form-label">
                                {{ $data->end }}
                            </div>
                            @break
                        @case('狀態')
                            <div class="col-form-label">
                                {!! $data->StatusCss !!}
                            </div>
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