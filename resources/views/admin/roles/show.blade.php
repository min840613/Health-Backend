@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <h1>權限群組資訊</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <div class="form-group">
                <label for="inputName">群組名稱</label>
                <div>{{ $role->name }}</div>
            </div>
            <div class="form-group">
                <label for="inputRoles">權限</label>
                @foreach($permission as $key => $value)
                <div class="row">
                    <label for="{{ $value['role_name'] }}" class="col-sm-2 control-label form-label categories" style="color: blue;">
                        {{ $value['text'] }}
                    </label>
                    <div class="col-sm-10" style="margin-bottom">
                        <div class="row">
                            @foreach($value['submenu'] as $sub_value)
                            @if ( isset($sub_value['submenu']) && $sub_value['submenu'] )
                            <label for="{{ $sub_value['role_name'] }}" class="col-sm-2 control-label form-label categories" style="color: green;">
                                {{ $sub_value['text'] }}
                            </label>
                            <div class="col-sm-10" style="margin-bottom">
                                <div class="row">
                                    @if ( isset($sub_value['submenu']) )
                                    @foreach($sub_value['submenu'] as $sub2_value)
                                    <label for="{{ $sub2_value['role_name'] }}" class="col-sm-2 control-label categories form-label">
                                        {{ $sub2_value['text'] }}
                                    </label>
                                    <div class="col-sm-10" style="margin-bottom">
                                        <?php $roles_category = array('list','create','edit','delete') ?>
                                        @foreach($roles_category as $roles_value)
                                        {{ Form::checkbox('permission[]', $sub2_value['role_name'].'-'.$roles_value, in_array($sub2_value['role_name'].'-'.$roles_value, $rolePermissions) ? true : false, array('class' => 'values', 'style' => 'margin-top: 6px;', 'id' => $sub2_value['role_name'].'-'.$roles_value, 'disabled')) }}
                                        <label for="{{ $sub2_value['role_name'].'-'.$roles_value }}">
                                            <?php
                                                switch($roles_value){
                                                    case 'list':
                                                        echo '瀏覽';
                                                        break;
                                                    case 'create':
                                                        echo '新增';
                                                        break;
                                                    case 'edit':
                                                        echo '修改';
                                                        break;
                                                    case 'delete':
                                                        echo '刪除';
                                                        break;
                                                }
                                            ?>
                                        </label>
                                        @endforeach
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                            @else
                            <label for="{{ $sub_value['role_name'] }}" class="col-sm-2 control-label form-label categories" style="">
                                {{ $sub_value['text'] }}
                            </label>
                            <div class="col-sm-10" style="margin-bottom">
                                <?php $roles_category = array('list','create','edit','delete') ?>
                                @foreach($roles_category as $roles_value)
                                {{ Form::checkbox('permission[]', $sub_value['role_name'].'-'.$roles_value, in_array($sub_value['role_name'].'-'.$roles_value, $rolePermissions) ? true : false, array('class' => 'values', 'style' => 'margin-top: 6px;', 'id' => $sub_value['role_name'].'-'.$roles_value, 'disabled')) }}
                                <label for="{{ $sub_value['role_name'].'-'.$roles_value }}">
                                    <?php
                                        switch($roles_value){
                                            case 'list':
                                                echo '瀏覽';
                                                break;
                                            case 'create':
                                                echo '新增';
                                                break;
                                            case 'edit':
                                                echo '修改';
                                                break;
                                            case 'delete':
                                                echo '刪除';
                                                break;
                                        }
                                    ?>
                                </label>
                                @endforeach
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
                @endforeach
            </div>
            {{-- <a href="{{ route('roles.index') }}">
                <button type="button" class="btn btn-outline-info btn-flat">Back</button>
            </a> --}}
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