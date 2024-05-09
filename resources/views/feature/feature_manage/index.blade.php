@extends('adminlte::page')

@section('title', 'Features')

@section('content_header')
    <h1>Feature管理</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="float-left mr-3">
                <a href="{{ route('feature_manage.create') }}"><button type="button" class="btn btn-block btn-success btn-flat float-right px-4">新增</button></a>
            </div>
        </div>
        @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
        @endif
        <div class="card-body p-0">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 10px">ID</th>
                        <th>名稱</th>
                        <th>feature</th>
                        <th>敘述</th>
                        <th>狀態</th>
                        <th>動作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($features as $key => $feature)
                    <tr>
                        <td>{{ $feature->id }}</td>
                        <td>{{ $feature->title }}</td>
                        <td>{{ $feature->feature }}</td>
                        <td>{{ $feature->description }}</td>
                        <td>
                            @if ($feature->active_at)
                            <button type="button" class="btn btn-block btn-outline-primary btn-flat">開啟</button>
                            @else
                            <button type="button" class="btn btn-block btn-outline-secondary btn-flat">關閉</button>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-info btn-flat" href="{{ route('feature_manage.show', $feature->id) }}">預覽</a>
                            @can('feature_manage-edit')
                            <a class="btn btn-primary btn-flat" href="{{ route('feature_manage.edit', $feature->id) }}">編輯</a>
            			    @endcan

                            @if (!$feature->active_at)
                                @can('feature_manage-delete')
                                {!! Form::open(['method' => 'DELETE','route' => ['feature_manage.destroy', $feature->id],'style'=>'display:inline']) !!}
                                {!! Form::button('刪除', ['class' => 'delete btn btn-danger btn-flat']) !!}
                                {!! Form::close() !!}
                                @endcan
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{!! $features->render() !!}
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $('.delete').on('click', function() {
            item = $(this).parent('form')
            Swal.fire({
                title: '確定要刪除這筆資料？',
                icon: 'question',
                iconColor: '#FA5E6A',
                showDenyButton: true,
                confirmButtonText: `刪除！`,
                confirmButtonColor: '#FA5E6A',
                denyButtonText: `取消`,
                denyButtonColor: 'gray'
            }).then((result) => {
                if (result.isConfirmed) {
                    item.submit();
                }
            })
        })
    </script>
@stop
