@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <h1>使用者資訊</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label class="mr-4" for="inputName">名稱</label>
        {{ $user->name }}
            </div>
            <div class="form-group">
                <label class="mr-4" for="inputEmail">帳號</label>
        {{ $user->email }}
            </div>
            <div class="form-group">
                <label class="mr-4" for="inputRoles">權限</label>
        @if(!empty($user->getRoleNames()))
                    @foreach($user->getRoleNames() as $v)
                    <label class="badge badge-success">{{ $v }}</label>
                    @endforeach
                @endif
            </div>
        {{-- <a href="{{ route('users.index') }}"><button type="button" class="btn btn-outline-info btn-flat">返回</button></a> --}}
        <input type="button"name="back" class="btn btn-outline-info" value="返回"onClick="javascript:history.back()">
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
