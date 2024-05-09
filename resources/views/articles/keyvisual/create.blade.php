@extends('adminlte::page')

@section('title', '頭條管理')

@section('content_header')
    <h1>頭條管理 - 新增</h1>
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
        {!! Form::open(array('route' => 'keyvisual.store','method'=>'POST')) !!}
            <div class="card-body">
                {!! Form::text('source_id', ($data?$data['source_id']:null), array('placeholder' => '文章ID','class' => 'form-control d-none', 'id' => 'inputArticleID')) !!}
                @foreach($field as $v)
                    @switch($v['title'])
                        @case('文章標題')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="inputTitle">
                                    {{$v['title']}}
                                    @if($v['is_required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-10">
                                    {!! Form::text('title', ($data?$data['title']:null), array('placeholder' => $v['title'],'class' => 'form-control', 'id' => 'inputTitle')) !!}
                                    <p><small>標題字數:大-最多40字,小-最多24字</small></p>
                                </div>
                            </div>
                            @break
                        @case('頭條URL')
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
                        @case('首頁主圖路徑')
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
                        @case('發佈時間')
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="inputStart">
                                    {{$v['title']}}
                                    @if($v['is_required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-4">
                                    <x-adminlte-date-range name="start" >
                                        <x-slot name="appendSlot">
                                        <div class="input-group-text bg-gradient-info">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        </x-slot>
                                    </x-adminlte-date-range>
                                </div>
                                <label class="col-sm-2 col-form-label" for="inputEnd">
                                    下架時間
                                    @if($v['is_required'])
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <div class="col-sm-4">
                                    <x-adminlte-date-range name="end" >
                                        <x-slot name="appendSlot">
                                        <div class="input-group-text bg-gradient-info">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        </x-slot>
                                    </x-adminlte-date-range>
                                </div>
                            </div>
                            @break
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
@stop

@include('components.gallery_modal')

@section('css')
   <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
   <script>
       $().ready(function(){


           $('input[name="start"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  moment().startOf('hour'),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });

           $('input[name="end"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  moment().add(7, 'days').format('YYYY-MM-DD 23:59'),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });

            $("#review_image_click").click(function(){
                if($("#inputImage").val()){
                    $("#review_image").attr("src",$("#inputImage").val());
                    $("#review_image_div").show();
                }
            })
            $('#gallery_image').on('click', function() {
                var parentId = $(this).parent('div').prev('div').children('input').attr('id');
                console.log(parentId);
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
       })

       function historylist(){
            if (history.length > 1) {
                history.back();
            } else {
                window.close();                
            }
        }
   </script>
@stop
