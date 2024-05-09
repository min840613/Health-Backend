@extends('adminlte::page')

@section('title', '推播管理')

@section('content_header')
    <h1>推播管理 - 修改</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            {!! Form::open(['url' => route('notifications.update',['notification' => $notification->id]),'method'=>'PUT']) !!}
            <input type="hidden" name="is_need_detail" value="{{$is_need_detail}}">
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">推播類型</label>
                    <div class="col-sm-10">
                        @switch($notification->type)
                            @case(1)
                            文章 (ID:{{$notification->source_id}})
                            @break
                            @case(3)
                            訊息通知
                            @break
                            @case(4)
                            活動公告
                            @break
                            @case(5)
                            APP搖一搖 (ID:{{$notification->source_id}})
                            @break
                        @endswitch
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required" for="prepush">推播時間</label>
                    <div class="col-sm-10">
                        <input type="hidden" id="prepushOrigin" value="{{$notification['prepush']}}" >
                        <x-adminlte-date-range name="prepush"   igroup-size="sm" placeholder="請選擇推播時間" id="prepush" autocomplete="off">
                            <x-slot name="appendSlot">
                            <div class="input-group-text bg-dark">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            </x-slot>
                        </x-adminlte-date-range>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">推播標題</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="message" igroup-size="sm" value="{{$notification['message']}}"
                                          required/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">推播內容</label>
                    <div class="col-sm-10">
                        <x-adminlte-textarea name="message_body" rows=5
                                             required>{{$notification['message_body']}}</x-adminlte-textarea>
                    </div>
                </div>
                @if(!$is_need_detail)
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">內容類型</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="content_type" igroup-size="sm" required>
                            <option value="1" {{$notification->content_type == '1' ? 'selected' : ''}}>輸入URL</option>
                            <option value="2" {{$notification->content_type == '2' ? 'selected' : ''}}>內容頁上稿</option>
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row content_type_unique" id="content_type_1">
                    <label class="col-sm-2 col-form-label column-required">輸入URL</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="url" igroup-size="sm" value="{{$notification['url']}}"/>
                    </div>
                </div>
                <div class="form-group row content_type_unique" id="content_type_2">
                    <label class="col-sm-2 col-form-label column-required">內容頁上稿</label>
                    <div class="col-sm-10">
                        <x-adminlte-textarea name="content">{{$notification['content']}}</x-adminlte-textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">圖片URL</label>
                    <div class="row col-sm-10">
                        <div class="col-sm-9">
                            <x-adminlte-input name="image" igroup-size="sm" type="url"
                                              value="{{$notification['image']}}" required/>
                        </div>
                        <div class="col-sm-3">
                            <a id="gallery_image" class="btn btn-success" href="javascript:void(0)">圖庫</a>
                            <a id="review_image_click" class="btn btn-success ml-4" href="javascript:void(0)">預覽</a>
                        </div>
                        <div id="review_image_div" class="col-sm-2" style="display: none;">
                            <div class="row">
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10"><img id="review_image" style="width: 100%"/></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div class="card-footer">
                <button type="submit" class="btn btn-primary mr-4">儲存</button>
                <input type="button" name="back" class="btn btn-outline-info" value="返回" onClick="historylist()">
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    @include('components.gallery_modal')
@stop

@section('css')
    <style>
        .column-required::after {
            content: ' *';
            color: red;
        }
    </style>
@stop

@section('js')
    @include('components.tinymce')
    <script>
        init()

        function init() {
            $('.content_type_unique').hide()
            $('#content_type_' + $('[name=content_type]').val()).show()
        }

        $(document).ready(function () {
            $('[name=content_type]').on('change', function () {
                $('.content_type_unique').hide()
                $('#content_type_' + $(this).val()).show()
            })

           $('input[name="prepush"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  $("#prepushOrigin").val(),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });

            // 預覽
            $("#review_image_click").click(function () {
                if ($("#image").val()) {
                    $("#review_image").attr("src", $("#image").val());
                    $("#review_image_div").show();
                }
            })

            //圖庫
            $('#gallery_image').on('click', function () {
                var parentId = $('#image').attr('id');
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

            $('textarea#content').tinymce({
                'token': $('form input[name="_token"]').val()
            })
        })

        function historylist() {
            if (history.length > 1) {
                history.back();
            } else {
                window.close();
            }
        }
    </script>
@stop
