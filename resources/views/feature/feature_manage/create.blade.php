@extends('adminlte::page')

@section('title', 'Features')

@section('content_header')
    <h1>新增Feature</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        {!! Form::open(array('route' => 'feature_manage.store','method'=>'POST')) !!}
        @csrf
            <div class="card-body">
                <div class="form-group">
                    <label for="inputTitle">名稱</label>
                    {!! Form::text('title', null, array('placeholder' => 'Title','class' => 'form-control', 'id' => 'inputTitle')) !!}
                </div>
                <div class="form-group">
                    <label for="inputFeature">feature</label>
                    {!! Form::text('feature', null, array('placeholder' => 'Feature','class' => 'form-control', 'id' => 'inputFeature')) !!}
                </div>
                <div class="form-group">
                    <label for="inputDesc">description</label>
                    {!! Form::textarea('description', null, array('placeholder' => 'Description','class' => 'form-control', 'id' => 'inputDesc')) !!}
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input name="active_at" type="checkbox" class="custom-control-input" id="customSwitch1">
                        <!-- {!! Form::checkbox('active_at', null, array('class' => 'custom-control-input', 'id' => 'customSwitch1')) !!} -->
                        <label class="custom-control-label" for="customSwitch1">開啟</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-flat">新增</button>
		<a href="{{ route('feature_manage.index') }}"><button type="button" class="btn btn-outline-info btn-flat">返回</button>
            </div>
        {!! Form::close() !!}
   </div>
</div>
@stop

@section('css')

@stop

@section('js')

@stop
