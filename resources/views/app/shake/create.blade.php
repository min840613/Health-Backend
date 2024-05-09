@extends('adminlte::page')

@section('title', 'APP搖一搖 - 新增')

@section('content_header')
    <h1>APP搖一搖 - 新增</h1>
@stop

@section('content')
    <div class="col-md-12">
        <div class="card">
            {!! Form::open(['route' => 'shake.store','method'=>'POST']) !!}
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">活動類型</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="shake_type" igroup-size="sm" required>
                            <option value="2">一般活動</option>
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">開啟類型</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="shake_content_type" igroup-size="sm" required>
                            <option value="1" {{old('shake_content_type') == 1 ? 'selected' : ''}}>網址</option>
                            <option value="2" {{old('shake_content_type') == 2 ? 'selected' : ''}}>文章ID</option>
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required" name="shake_url_label">輸入URL</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="shake_url" value="{{old('shake_url')?old('shake_url'):null}}" igroup-size="sm" required/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">是否為EC連結</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="is_ec_connect" igroup-size="sm">
                            @if ( is_numeric(old('is_ec_connect')) )
                                <option value="1" {{old('is_ec_connect') == 1 ? 'selected' : ''}} >是</option>
                                <option value="0" {{old('is_ec_connect') == 0 ? 'selected' : ''}}>否</option>
                            @else
                                <option value="1" >是</option>
                                <option value="0" selected>否</option>
                            @endif
                        </x-adminlte-select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">標題</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="shake_title" value="{{old('shake_title')?old('shake_title'):null}}" igroup-size="sm" required/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">文案內容</label>
                    <div class="col-sm-10">
                        <x-adminlte-input name="content" value="{{old('content')?old('content'):null}}" igroup-size="sm" required/>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">活動開始時間</label>
                    <div class="col-sm-2">
                        @if ( old('shake_time_start') )
                            <input type="hidden" id="shake_time_start_origin" value="{{old('shake_time_start')}}">
                        @else
                            <input type="hidden" id="shake_time_start_origin" value="{{now()->addMinute()->toDateTimeString()}}">
                        @endif
                            <x-adminlte-date-range name="shake_time_start" igroup-size="sm" >
                                <x-slot name="appendSlot">
                                <div class="input-group-text bg-dark">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                </x-slot>
                            </x-adminlte-date-range>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">活動結束時間</label>
                    <div class="col-sm-2">
                        @if ( old('shake_time_end') )
                            <input type="hidden" id="shake_time_end_origin" value="{{old('shake_time_end')}}">
                        @else
                            <input type="hidden" id="shake_time_end_origin" value="{{now()->addDays(7)->endOfDay()->toDateTimeString()}}">
                        @endif
                            <x-adminlte-date-range name="shake_time_end" igroup-size="sm" >
                                <x-slot name="appendSlot">
                                <div class="input-group-text bg-dark">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                </x-slot>
                            </x-adminlte-date-range>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label column-required">活動狀態</label>
                    <div class="col-sm-10">
                        <x-adminlte-select name="shake_status" igroup-size="sm">
                            @if ( is_numeric(old('shake_status')) )
                                <option value="1" {{old('shake_status') == 1 ? 'selected' : ''}} >發佈</option>
                                <option value="0" {{old('shake_status') == 0 ? 'selected' : ''}}>下架</option>
                            @else
                                <option value="1" selected>發佈</option>
                                <option value="0">下架</option>
                            @endif
                        </x-adminlte-select>
                    </div>
                </div>
            </div>
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
    <script>
        $(document).ready(function () {
            $('[name=shake_content_type]').on('change', function () {
                handleShakeContentTypeEvent($(this).val())
                $('[name=shake_url]').val('')
            });

           $('input[name="shake_time_start"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  $("#shake_time_start_origin").val(),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });
           $('input[name="shake_time_end"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                startDate:  $("#shake_time_end_origin").val(),
                timePicker: true,
                timePicker24Hour: true,
                cancelButtonClasses: "btn-danger",
                locale: {
                    format: 'YYYY-MM-DD HH:mm'
                }
            });

            handleShakeContentTypeEvent($("#shake_content_type").val());
        })

        // 依照"開啟類型"更換下方input的label文字
        const handleShakeContentTypeEvent = (shake_content_type_id) => {
            switch (shake_content_type_id) {
                case '1':
                    $('[name=shake_url_label]').html('輸入URL')
                    break
                case '2':
                    $('[name=shake_url_label]').html('文章ID')
                    break
            }
        };

        function historylist() {
            if (history.length > 1) {
                history.back();
            } else {
                window.close();
            }
        }
    </script>
@stop
