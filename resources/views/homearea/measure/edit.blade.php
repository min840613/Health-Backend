@extends('adminlte::page')

@section('title', '小工具量測管理')

@section('content_header')
    <h1>小工具量測管理 - 修改</h1>
@stop

@section('content')
<div class="col-md-12">
    <div class="card">
        @if ($message = Session::get('error'))
            <div class="alert alert-danger">
                <p class="m-0">{{ $message }}</p>
            </div>
        @endif
        @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        {!! Form::model($data, ['method' => 'PATCH','route' => ['measure.update', $data->id]]) !!}
            <div class="card-body">
                @foreach($field as $v)
                    @switch($v['title'])
                        @case('量測名稱')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="inputTitle">
                                    {{$v['title']}}
                                    @if($v['is_required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!! Form::text('title', ($data?$data['title']:null), array('placeholder' => $v['title'],'class' => 'form-control', 'id' => 'inputTitle')) !!}
                                </div>
                            </div>
                            @break
                        @case('量測URL')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="inputLink">
                                    {{$v['title']}}
                                    @if($v['is_required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!! Form::text('link', ($data?$data['link']:null), array('placeholder' => $v['title'],'class' => 'form-control', 'id' => 'inputLink')) !!}
                                </div>
                            </div>
                            @break
                        @case('主圖URL')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="inputImage">
                                    {{$v['title']}}
                                    @if($v['is_required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-7">
                                    {!! Form::text('image', ($data?$data['image']:null), array('placeholder' => $v['title'],'class' => 'form-control', 'id' => 'inputImage')) !!}
                                </div>
                                <div class="col-sm-3">
                                    <a id="gallery_image" class="btn btn-success btn-flat float-left px-4" href="javascript:;">圖庫</a>
                                    <a id="review_image_click" class="btn btn-secondary btn-flat float-left px-4 ml-4" href="javascript:;">預覽</a>
                                </div>
                                <div class="col-sm-2">
                                </div>
                                <div class="col-sm-10">
                                    <span style="color: red">備註：建議圖片尺寸為 78 * 78 px，並建議為SVG格式</span>
                                </div>
                                <div id="review_image_div" class="col-sm-12 mt-4" style="display: none;">
                                    <div class="row">
                                        <label class="col-sm-2"></label>
                                        <div class="col-sm-10">
                                            <img id="review_image" style="width: 100%" src="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @break
                        @case('上架時間')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="inputStart">
                                    {{$v['title']}}
                                    @if($v['is_required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-4">
                                    @php
                                        $StartDateConfig = ['format' => 'YYYY-MM-DD HH:mm'];
                                    @endphp
                                    <x-adminlte-input-date name="start" value="{{ $data?$data['start']:null }}" :config="$StartDateConfig">
                                        <x-slot name="appendSlot">
                                            <div class="input-group-text bg-gradient-info">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-input-date>
                                </div>
                                <label class="col-sm-2 col-form-label" for="inputStart">
                                    下架時間
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="col-sm-4">
                                    @php
                                        $EndDateConfig = ['format' => 'YYYY-MM-DD HH:mm'];
                                    @endphp
                                    <x-adminlte-input-date name="end" value="{{ $data?$data['end']:null }}" :config="$EndDateConfig">
                                        <x-slot name="appendSlot">
                                            <div class="input-group-text bg-gradient-info">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                        </x-slot>
                                    </x-adminlte-input-date>
                                </div>
                            </div>
                            @break
                        @case('狀態')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="inputEnd">
                                    {{$v['title']}}
                                    @if($v['is_required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    <x-adminlte-select name="status">
                                        <option value="1" {{$data->status == '1' ? 'selected' : ''}}>發佈</option>
                                        <option value="0" {{$data->status == '0' ? 'selected' : ''}}>下架</option>
                                    </x-adminlte-select>
                                </div>
                            </div>
                    @endswitch
                @endforeach
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-flat mr-4">儲存</button>
                <input type="button" name="back" class="btn btn-outline-info" value="返回" onClick="historylist()">
            </div>
        {!! Form::close() !!}
   </div>
</div>
@include('components.gallery_modal')
@stop

@section('css')
   <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $().ready(function(){
            $("#review_image_click").click(function(){
                if($("#inputImage").val()){
                    $("#review_image").attr("src",$("#inputImage").val());
                    $("#review_image_div").show();
                }
            })

            $('#gallery_image').on('click', function() {
                var parentId = $(this).parent('div').prev('div').children('input').attr('id');
                $('#galleryModal iframe').attr('data-parent', parentId );
                $('#galleryModal').modal('show');
            })

            window.addEventListener("message", (e) => {
                if(e.origin !== 'tvbs.com.tw' || e.origin !== 'test.health-backstage.com') {
                    $("#" + e.data.parent).val(e.data.imgUrl);
                    $(".gallery-modal-header > .close").trigger('click');
                    $("#review_image_div").hide();
                }
            });
        });

        function historylist(){
            if (history.length > 1) {
                history.back();
            } else {
                window.close();                
            }
        }
    </script>
@stop
