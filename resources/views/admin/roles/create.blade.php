@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <h1>新增權限群組</h1>
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
        {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
        <div class="card-body">
            <div class="form-group">
                <label for="inputName">群組名稱</label>
                {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control', 'id' => 'inputName')) !!}
            </div>
            <div class="form-group permission_group">
                <label for="inputPermission">權限</label>
                <div class="accordion" id="accordionExample">
                    @foreach($permission as $key => $value)
                    <div class="card">
                        <div class="card-header" id="heading_{{ $value['role_name'] }}">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse_{{ $value['role_name'] }}" aria-expanded="true" aria-controls="collapse_{{ $value['role_name'] }}">
                                    {{ $value['text'] }}
                                </button>
                            </h2>
                        </div>
                        <div id="collapse_{{ $value['role_name'] }}" class="collapse" aria-labelledby="heading_{{ $value['role_name'] }}" data-parent="#accordionExample">
                            <div class="card-body">
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
                                                {{ Form::checkbox('permission[]', $sub2_value['role_name'].'-'.$roles_value, false, array('class' => 'values', 'style' => 'margin-top: 6px;', 'id' => $sub2_value['role_name'].'-'.$roles_value)) }}
                                                <label for="{{ $sub2_value['role_name'].'-'.$roles_value }}">
                                                    <?php
                                                        switch($roles_value){
                                                            case 'list':
                                                                echo '列表';
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
                                        {{ Form::checkbox('permission[]', $sub_value['role_name'].'-'.$roles_value, false, array('class' => 'values', 'style' => 'margin-top: 6px;', 'id' => $sub_value['role_name'].'-'.$roles_value)) }}
                                        <label for="{{ $sub_value['role_name'].'-'.$roles_value }}">
                                            <?php
                                                switch($roles_value){
                                                    case 'list':
                                                        echo '列表';
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
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary btn-flat mr-4">儲存</button>
            {{-- <a href="{{ route('roles.index') }}"><button type="button" class="btn btn-outline-info btn-flat">返回</button></a> --}}
            <input type="button"name="back" class="btn btn-outline-info" value="返回"onClick="javascript:history.back()">
        </div>
        {!! Form::close() !!}
    </div>
</div>
@stop
@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
@stop
@section('js')
<script>
    $().ready(function(){
        $(".categories").click(function(){
            categories_name = $(this).attr('for');
            checked = false;
            $("input[id^="+categories_name+"]").each(function(){
                if($(this).prop("checked")){
                    checked = true;
                }
            })
            if(checked){
                $("input[id^="+categories_name+"]").prop('checked',false);
            } else {
                $("input[id^="+categories_name+"]").prop('checked',true);
            }
        })
        $("input[id$='-list']").click(function(){
            if(!$(this).prop('checked')){
                $(this).parent("div").find("input[id$='-create']").prop('checked',false);
                $(this).parent("div").find("input[id$='-edit']").prop('checked',false);
                $(this).parent("div").find("input[id$='-delete']").prop('checked',false);
            }
        });
        $("input[id$='-create']").click(function(){
            if($(this).prop('checked')){
                $(this).parent("div").find("input[id$='-list']").prop('checked',true);
            }
        });
        $("input[id$='-edit']").click(function(){
            if($(this).prop('checked')){
                $(this).parent("div").find("input[id$='-list']").prop('checked',true);
            }
        });
        $("input[id$='-delete']").click(function(){
            if($(this).prop('checked')){
                $(this).parent("div").find("input[id$='-list']").prop('checked',true);
            }
        })
    })
</script>
@stop
