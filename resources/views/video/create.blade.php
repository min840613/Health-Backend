@extends('adminlte::page')

@section('title', '影片管理-新增')

@section('content_header')
    <h1>影片管理-新增</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">

            <div class="card-body p-0">
    <div class="file-overlay">
        <span class="overlay-text">影片上傳中<div class="lds-hourglass"></div></span>
        

    </div>
                {!! Form::open(array('route' => 'video.upload','method'=>'POST', 'files' => true, 'id'=> 'form-upload')) !!}
                {{ csrf_field() }}

                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <strong>發生錯誤</strong> 請檢查必填欄位<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    <div class="card-body">
                        @foreach($field as $fieldKey => $fieldValue)
                            @switch($fieldValue['type'])
                                @case('album_select')
                                    <div class="form-group row ">
                                        <label class="col-sm-2 col-form-label" for="{{ $fieldKey }}">
                                            {{$fieldValue['title']}}
                                            @if($fieldValue['required'])
                                            <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <div class="col-sm-10 row">
                                            <div class="col-sm-3">
                                                <x-adminlte-select2 name="{{$fieldKey}}" igroup-size="sm">
                                                    <option value="0" selected>請選擇</option>
                                                    @foreach($fieldValue['options'] as $options)
                                                        <option value="{{$options->id}}">{{$options->title}}</option>
                                                    @endforeach
                                                </x-adminlte-select2>
                                            </div>

                                        </div>
                                    </div>
                                    
                                    @break
                                    @case('text')
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="{{ $fieldKey }}">
                                                {{$fieldValue['title']}}
                                                @if($fieldValue['required'])
                                                <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-sm-3">
                                                {!!
                                                    Form::text($fieldKey, null, array(
                                                        'placeholder' => $fieldValue['placeholder'],
                                                        'class' => 'form-control',
                                                        'id' => $fieldKey
                                                    ))
                                                !!}
                                            </div>
                                        </div>
                                        @break
                                    @case('video')
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label" for="{{ $fieldKey }}">
                                                {{$fieldValue['title']}}
                                                @if($fieldValue['required'])
                                                <span class="text-danger">*</span>
                                                @endif
                                            </label>
                                            <div class="col-sm-3">
                                                {!!
                                                    Form::file($fieldKey)
                                                !!}
                                            </div>
                                        </div>
                                        @break
                            @endswitch
                        @endforeach
                    </div>
                    <div class="card-footer">
                        @can($role_name.'-create')
                        <button type="submit" class="btn btn-primary btn-flat mr-4">儲存</button>
                        @endcan
                        <input type="button" name="back" class="btn btn-outline-info" value="返回" onClick="historylist()">
                    </div>
                {!! Form::close() !!}


            </div>

        </div>
    </div>

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

    $("#form-upload").submit(function(e){
        $(".file-overlay").show();
    })
</script>
@stop