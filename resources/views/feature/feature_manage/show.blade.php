@extends('adminlte::page')

@section('title', 'Features')

@section('content_header')
    <h1>Features</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="inputTitle">名稱</label>
		        {{ $feature->title }}
            </div>
            <div class="form-group">
                <label for="inputFeature">Feature</label>
		        {{ $feature->feature }}
            </div>
            <div class="form-group">
                <label for="inputDesc">Description</label>
		        {{ $feature->description }}
            </div>
            <div class="form-group">
                <label for="inputDesc">開啟</label>
                @if(!empty($feature->active_at))
                是
                @else
                否
                @endif
            </div>

	    <a href="{{ route('feature_manage.index') }}"><button type="button" class="btn btn-outline-info btn-flat">返回</button>
        </div>
   </div>
</div>
@stop

@section('css')
   <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
   <script> console.log('Hi!'); </script>
@stop
