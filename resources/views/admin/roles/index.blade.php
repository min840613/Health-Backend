@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <h1>權限群組管理</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="float-left mr-3">
                <a href="{{ route('roles.create') }}"><button type="button" class="btn btn-block btn-success btn-flat float-right px-4">新增</button></a>
            </div>
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
                        <th style="width: 10px">ID</th>
                        <th>群組名稱</th>
                        <th>動作</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($roles as $key => $role)
                    <tr>
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>
                            <a class="btn btn-secondary btn-flat mr-4" href="{{ route('roles.show', $role->id) }}">檢視</a>
                @can('admin_roles-edit')
                            <a class="btn btn-primary btn-flat mr-4" href="{{ route('roles.edit', $role->id) }}">編輯</a>
                @endcan
                @can('admin_roles-delete')
                        {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                        {!! Form::button('刪除', ['class' => 'delete btn btn-danger btn-flat']) !!}
                        {!! Form::close() !!}
                        @endcan
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{!! $roles->render() !!}
@stop

@section('css')
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
@stop

@section('js')
<script>
    $('.delete').on('click', function() {
        item = $(this).parent('form')
        Swal.fire({
            title: '確定要刪除這筆資料？',
            icon: 'question',
            iconColor: '#f87e6c',
            showDenyButton: true,
            confirmButtonText: `刪除！`,
            confirmButtonColor: '#f87e6c',
            denyButtonText: `取消`,
            denyButtonColor: '#9c9996'
        }).then((result) => {
            if (result.isConfirmed) {
                item.submit();
            }
        })
    })
</script>
@stop
